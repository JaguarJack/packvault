<?php
namespace App\Services\OAuth;


use Overtrue\Socialite\Contracts\UserInterface;

/**
 * 接入 self host gitea 应用
 *
 * gitea oauth 授权
 */
class Gitea extends Socialite
{
    public function redirect()
    {
        $url = $this->socialiteManager->create('gitea')->redirect();

        return redirect($url);
    }

    public function callback(): UserInterface
    {
        $code = request()->get('code');

        return $this->socialiteManager->create('gitea')->setGuzzleOptions(['verify' => false])->userFromCode($code);
    }
}
