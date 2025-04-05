<?php
namespace App\Services\OAuth;

use App\Support\GiteaProvider;
use Overtrue\Socialite\SocialiteManager;

class Socialite
{
    protected ?SocialiteManager $socialiteManager = null;

    public function __construct()
    {
        if (! $this->socialiteManager) {
            $this->socialiteManager = new SocialiteManager($this->config());

            $this->socialiteManager->extend('gitea', function (array $config) {
                return new GiteaProvider($config);
            });
        }
    }

    protected function config()
    {
        return config('socialite');
    }
}
