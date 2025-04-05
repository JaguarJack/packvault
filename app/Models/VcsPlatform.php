<?php

namespace App\Models;

use App\Enums\Vcs;
use App\Services\OAuth\Gitee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;


/**
 * @property numeric $user_id
 * @property numeric $team_id
 * @property string $vcs
 * @property string $access_token
 * @property string|numeric $oauth_user_id
 */
class VcsPlatform extends Model
{
    //
    protected $fillable = [
        'user_id',
        'team_id',
        'vcs',
        'access_token',
        'oauth_user_id'
    ];

    /**
     * 获取 github access token
     *
     * @return mixed
     */
    public static function getUserGithubAccessToken(): mixed
    {
        return self::query()->where('user_id', Auth::id())->where('vcs', Vcs::GITHUB)->value('access_token');
    }

    /**
     * 获取用户 gitee access token
     *
     * @param null $userId
     * @return mixed
     */
    public static function getUserGiteeAccessToken($userId = null): mixed
    {
        $userId = $userId ? : Auth::id();

        return self::query()->where('user_id', $userId)->where('vcs', Vcs::GITEE)->value('access_token');
    }

    /**
     * 获取用户 gitee access token
     *
     * @return mixed
     */
    public static function getUserCodingAccessToken(): mixed
    {
        return self::query()->where('user_id', Auth::id())->where('vcs', Vcs::CODING)->value('access_token');
    }

    /**
     * @param mixed $accessToken
     * @param $userId
     * @return mixed
     */
    public function setGiteeAccessToken(mixed $accessToken, $userId = null): mixed
    {
        $userId = $userId ? : Auth::id();

        return self::query()->where('user_id', $userId)->where('vcs', Vcs::GITEE)->update(['access_token' => $accessToken]);
    }

    /**
     * 获取已授权的 app
     *
     * @param string $fields
     * @return Collection
     */
    public static function getConnectedApps(string|array $fields = '*'): Collection
    {
        return self::query()->where('user_id', Auth::id())->whereNotNull('access_token')->get($fields);
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }
}
