<?php
namespace App\Http\Controllers;

use App\Jobs\PackageBuildJob;
use App\Models\Team;
use App\Models\BuildPackageJob;
use App\Models\VcsPlatform;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function github(Request $request)
    {
        $data = $request->all();

        if ($data['ref_type'] == 'tag') {
            Log::info('start');
            $repositoryName = $data['repository']['full_name'];
            $repositoryUrl = $data['repository']['html_url'];
            $repository = Package::query()
                            ->where('repo_name', $repositoryName)
                            ->where('url', $repositoryUrl)
                            ->first();

            if ($repository) {
                $connectApp = VcsPlatform::query()->where('oauth_user_id', $data['sender']['id'])->first();
                if ($connectApp) {
                    // 验证所有权
                    Log::info($connectApp->user_id);
                    if ($connectApp->user_id == $repository->user_id) {
                        // 获取当前用户的 team
                        $team = Team::query()->where('creator_id', $connectApp->user_id)->first();
                        // 团队存在
                        if ($team) {
                            $job = BuildPackageJob::createJob($repository->id, $repository->user_id, $data['ref']);
                            BuildPackageJob::dispatch($team, $repository, $job, $connectApp->getAccessToken());
                        }
                    }
                }
            }
        }
    }
}
