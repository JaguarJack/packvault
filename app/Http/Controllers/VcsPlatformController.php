<?php
namespace App\Http\Controllers;

use App\Enums\Vcs;
use App\Models\VcsPlatform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VcsPlatformController extends Controller
{
    /**
     * @param VcsPlatform $teamConnectApp
     * @return Response
     */
    public function connectVcs(): Response
    {
        $vcsPlatform = VcsPlatform::query()->get(['vcs', 'access_token'])->keyBy('vcs')->toArray();

        return Inertia::render('vcs/connect', array_merge([
            'is_github_connected' => (bool) ($vcsPlatform[Vcs::GITHUB->value]['access_token'] ?? false),
            'is_gitee_connected' => (bool) ($vcsPlatform[Vcs::GITEE->value]['access_token'] ?? false),
            'is_coding_connected' => (bool) ($vcsPlatform[Vcs::CODING->value]['access_token'] ?? false),
            'is_gitlab_connected' => (bool) ($vcsPlatform[Vcs::GITLAB->value]['access_token'] ?? false),
            'is_gitea_connected' => (bool) ($vcsPlatform[Vcs::GITEA->value]['access_token'] ?? false),
        ], config('packvault.vcs')));
    }

    /**
     * @param $appName
     * @return RedirectResponse
     */
    public function disconnectVcs($vcs): RedirectResponse
    {
        $vcsPlatform = VcsPlatform::query()->where('vsc', $vcs)->first();

        if (! $vcsPlatform) {
            return to_route('connect.vcs');
        }

        $vcsPlatform->access_token = null;
        $vcsPlatform->save();

        return to_route('connect.vcs');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function storePersonalToken(Request $request): RedirectResponse
    {
        $accessToken = $request->get('access_token');
        $name = $request->get('name');

        if (! $accessToken) {
            return to_route('connect.vcs')->with('error', '请输入 personal_token');
        }

        if ($name) {
            $accessToken = "{$name}@{$accessToken}";
        }

        $vcs = $request->get('vcs');

        $vcsPlatform = VcsPlatform::query()->where('vcs', $vcs)->first();

        if ($vcsPlatform) {
            $vcsPlatform->update(['access_token' => $accessToken]);
        } else {
            $vcsPlatform = new VcsPlatform();
            $vcsPlatform->fill([
                'vcs' => $vcs,
                'access_token' => $accessToken,
                'user_id' => $this->userId(),
            ]);
            $vcsPlatform->save();
        }

        return to_route('connect.vcs')->with('success', "{$vcs} 授权成功");
    }
}
