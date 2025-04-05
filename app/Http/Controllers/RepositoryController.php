<?php
namespace App\Http\Controllers;

use App\Enums\BuildJobStatus;
use App\Enums\Vcs;
use App\Http\Controllers\Team\Team;
use App\Jobs\PackageBuildJob;
use App\Models\BuildPackageJob;
use App\Models\LicenseUser;
use App\Models\Package;
use App\Models\PackageDownload;
use App\Models\PackageTag;
use App\Models\VcsPlatform;
use App\Services\PackageService;
use App\Services\Repository\RepositoryFactory;
use App\Support\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RepositoryController extends Controller
{

    public function index()
    {
        $buildStatusQuery = BuildPackageJob::query()
            ->whereColumn('package_id', 'packages.id')
            ->whereIn('status', [BuildJobStatus::RUNNING, BuildJobStatus::UN_START])
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->orderByDesc('id')
            ->select('status')->limit(1);

        $repositories = Package::query()
                            ->addSelect(['build_status' =>$buildStatusQuery])
                            ->orderByDesc('id')
                            ->get();

        return Inertia::render('repository/index', [
            'repositories' => $repositories
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('repository/create');
    }

    /**
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        return Inertia::render('repository/detail', [
            'package' => function () use ($id) {
                $package = Package::query()->with([
                    'tags' => function ($query) {
                        $query->orderBy('name');
                    }
                ])
                ->addSelect([
                    'downloads' => PackageDownload::query()->whereColumn('package_id', 'packages.id')->select(DB::raw('count(*)')),
                ])
                 ->findOrFail($id);

                $package->installs = $package->tags->sum('download_times');
                return $package;
            },

            'building' => function () use ($id) {
                $status = BuildPackageJob::query()->where('package_id', $id)->orderByDesc('id')->value('status');
                return in_array($status, [BuildJobStatus::RUNNING->value, BuildJobStatus::UN_START->value]);
            }
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'url' => ['required', Rule::unique('packages', 'url')->whereNull('deleted_at'),]
        ], [
            'url.required' => '选择仓库',
            'url.unique' => '仓库已添加'
        ]);

        $url = Str::of($request->get('url'));

        $vcs = [
            'github.com' => Vcs::GITHUB->value,
            'gitee.com' => Vcs::GITHUB->value,
        ][parse_url($url, PHP_URL_HOST)];

        try {
            $packageInfo = RepositoryFactory::make($vcs)->setUrl($url->remove('.git')->toString())->getPackageInfo();
        } catch (\Exception $exception) {
            return to_route('repository.create')->withErrors(['url' => $exception->getMessage()]);
        }

        $userId = $this->userId();

        // 本地数据库检查
        if (Package::query()->where('name', $packageInfo['name'])->exists()) {
            return to_route('repository.create')->withErrors(['url' => "仓库的包名 {$packageInfo['name']} 已被注册，请先修改包名"]);
        }

        // 公共 packagist 库检查
        if (Helper::checkPackageInPackagist($packageInfo['name'])) {
            return to_route('repository.create')->withErrors(['url' => "仓库的包名 {$packageInfo['name']} 已被注册，请先修改包名"]);
        }

        $teamRepository = new Package;
        $data['url'] = $url->when(!$url->endsWith('.git'), function ($url) { $url->append('.git');})->toString();
        $data['description'] = $request->get( 'description') ? : $packageInfo['description'];
        $data['name'] = $packageInfo['name'];
        $data['repo_name'] = $packageInfo['repo_name'];
        $data['user_id'] = $userId;
        $data['vcs_id'] = VcsPlatform::query()->where('vcs', $vcs)->value('id');
        $data['stay_at'] = $vcs;

        $teamRepository->fill($data);
        if ($teamRepository->save()) {
            $this->updateSatisJson();
        }

        return to_route('repository');
    }

    /**
     * 更新包
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update($id, Request $request): RedirectResponse
    {
        $package = Package::query()->findOrFail($id);

        if (BuildPackageJob::hasUnstartOrRunning($package->id)) {
            return to_route('repository.show', $id)->with('error', "{$package->name} 正在构建中，请耐心等待...");
        }

        $accessToken = match ($package->stay_at) {
            Vcs::GITHUB->value => VcsPlatform::getUserGithubAccessToken(),
            Vcs::GITEE->value =>  VcsPlatform::getUserGiteeAccessToken(),
            Vcs::CODING->value => VcsPlatform::getUserCodingAccessToken()
        };

        if (! $accessToken) {
            return to_route('repository.show', $id)->with('error', "{$package->name} Access Token 未设置");
        }

        $buildPackageJob = BuildPackageJob::createJob($package->id, $package->name, $package->user_id, '');
        PackageBuildJob::dispatch($package, $buildPackageJob, $accessToken);

        $packageService = new PackageService();
        if (! file_exists($packageService->getSatisJsonPath())) {
            $this->updateSatisJson();
        }

        return to_route('repository.show', $id)->with('success', '推送更新成功，请等待构建');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $package = Package::query()->find($id);

        if ($package) {
            if ($package->licenses()->exists()) {
                return to_route('repository.show', $id)->with('error', "{$package->name} 已绑定 License 授权，请先删除 License 关联");
            }

            if ($package->delete()) {
                $package->tags()->delete();

                $this->updateSatisJson();
            }
        }

        return to_route('repository')->with(['success' => '删除成功']);
    }

    /**
     * 删除 tag
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroyTag($id): RedirectResponse
    {
        $tag = PackageTag::query()->where('id', $id)->first();

        $tag->delete();

        return to_route('repository.show', $tag->package_id);
    }

    /**
     * 更新 satis json 文件
     *
     * @return void
     */
    protected function updateSatisJson(): void
    {
        $teamPackages = Package::query()
            ->whereIn('vcs_id', VcsPlatform::getConnectedApps()->pluck('id'))
            ->get(['id', 'url'])->toArray();

        $packageService = new PackageService();

        $packageService->createSatisJson($teamPackages);
    }

    /**
     * 下载
     *
     * @param $id
     * @return Response
     */
    public function download($id)
    {
        return Inertia::render('team/Repository/Download', [
            'downloads' => PackageDownload::query()
                                    ->addSelect(
                                        [
                                            'username' => LicenseUser::query()->where('user_id', 'license_users.id')->select('email'),
                                        ]
                                    )
                                    ->where('package_id', $id)
                                    ->paginate(10)
        ]);
    }
}
