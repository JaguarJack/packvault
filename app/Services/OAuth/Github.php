<?php
namespace App\Services\OAuth;


use Illuminate\Support\Facades\Log;
use Overtrue\Socialite\SocialiteManager;
use Overtrue\Socialite\Contracts\UserInterface;

/**
 * github oauth 授权
 */
class Github extends Socialite
{
    public function redirect()
    {
        $url = $this->socialiteManager->create('github')->redirect();

        return redirect($url);
    }

    public function callback(): UserInterface
    {
        $code = request()->get('code');

        return $this->socialiteManager->create('github')->userFromCode($code);
    }
}
