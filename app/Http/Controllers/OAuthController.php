<?php

namespace App\Http\Controllers;

use App\Enums\Vcs;
use App\Models\Team;
use App\Models\VcsPlatform;
use App\Services\OAuth\Gitee;
use App\Services\OAuth\Github;
use Illuminate\Http\RedirectResponse;

class OAuthController extends Controller
{
    /**
     * @param Github $github
     * @return \Illuminate\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector|object
     */
    public function githubRedirect(Github $github)
    {
        return $github->redirect();
    }

    /**
     * @param Github $github
     * @param VcsPlatform $teamConnectApp
     * @return RedirectResponse
     */
    public function githubCallback(Github $github, VcsPlatform $teamVcsPlatforms): RedirectResponse
    {
        $githubUser = $github->callback();

        $userId = $this->userId();

        $vcsPlatform = $teamVcsPlatforms->where('user_id', $userId)->where('vcs', Vcs::GITHUB)->first();

        if (! $vcsPlatform) {
            $vcsPlatform = new VcsPlatform();
            $vcsPlatform->fill([
                'user_id' => $userId,
                'vcs' => Vcs::GITHUB->value,
                'access_token' => $githubUser->getAccessToken(),
                'oauth_user_id' => $githubUser->getId(),
            ]);
        } else {
            $vcsPlatform->access_token = $githubUser->getAccessToken();
        }
        $vcsPlatform->save();

        return to_route('connect.vcs');
    }

    public function giteeRedirect(Gitee $gitee)
    {
        return $gitee->redirect();
    }

    /**
     * @param Gitee $gitee
     * @return RedirectResponse
     */
    public function giteeCallback(Gitee $gitee): RedirectResponse
    {
        $giteeUser = $gitee->callback();

        $userId = $this->userId();

        $vcsPlatform = VcsPlatform::query()->where('user_id', $userId)->where('vcs', Vcs::GITEE)->first();

        $username = $giteeUser->getRaw()['login'];
        $accessToken = $giteeUser->getAccessToken();
        $accessToken = "{$username}@{$accessToken}";

        if (! $vcsPlatform) {
            $vcsPlatform = new VcsPlatform();
            $vcsPlatform->fill([
                'user_id' => $userId,
                'vcs' => Vcs::GITEE->value,
                'access_token' => $accessToken,
                'oauth_user_id' => $giteeUser->getId(),
            ]);
        } else {
            $vcsPlatform->access_token = $accessToken;
        }
        $vcsPlatform->save();

        // 缓存 refresh token
        if ($refreshToken = $giteeUser->getRefreshToken()) {
           $gitee->cacheRefreshToken($userId, $refreshToken);
        }

        return to_route('connect.vcs');
    }
}
