<?php
namespace App\Services\OAuth;

use App\Enums\Vcs;
use App\Models\VcsPlatform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * gitee oauth 授权
 */
class Gitee extends Socialite
{
    public function redirect()
    {
        $url = $this->socialiteManager->create('gitee')->redirect();

        return redirect($url);
    }

    public function callback()
    {
        $code = request()->get('code');

        return $this->socialiteManager->create('gitee')->userFromCode($code);
    }

    /**
     * @param $userId
     * @return string|null
     */
    public function newAccessToken($userId = null): ?string
    {
        $userId = $userId ? : Auth::user()->id;

        $refreshToken = $this->getCacheRefreshToken($userId);

        // 如果 refresh token 都过期了，取消授权
        if (! $refreshToken) {
            VcsPlatform::query()
                ->where('user_id', $userId)
                ->where('vcs', Vcs::GITEE)
                ->update(['access_token' => null]);

            return null;
        }

        $response = Http::post("https://gitee.com/oauth/token?grant_type=refresh_token&refresh_token=$refreshToken");

        if ($refreshToken = $response->json('refresh_token')) {
            $this->cacheRefreshToken($userId, $refreshToken);

            $giteeAccessToken = VcsPlatform::getUserGiteeAccessToken($userId);
            [$username, $accessToken] = explode('@', $giteeAccessToken);

            $teamVcsPlatform = new VcsPlatform();
            $newAccessToken = "{$username}@" . $response->json('access_token');

            $teamVcsPlatform->setGiteeAccessToken($newAccessToken, $userId);

            return $newAccessToken;
        }

        return null;
    }

    /**
     * @param $userId
     * @param $refreshToken
     * @return void
     */
    public function cacheRefreshToken($userId, $refreshToken): void
    {
        Cache::set('gitee_refresh_token_' . $userId, $refreshToken, 7 * 24 * 3600);
    }

    /**
     * @param $userId
     * @return string|null
     */
    public function getCacheRefreshToken($userId): ?string
    {
        return Cache::get('gitee_refresh_token_' . $userId);
    }
}
