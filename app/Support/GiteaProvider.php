<?php
namespace App\Support;

use GuzzleHttp\RequestOptions;
use Overtrue\Socialite\Contracts;
use Overtrue\Socialite\Contracts\ProviderInterface;
use Overtrue\Socialite\Providers\Base;
use Overtrue\Socialite\User;

class GiteaProvider extends Base implements ProviderInterface
{
    public const NAME = 'gitea';

    protected array $parameters = [
        'grant_type' => 'authorization_code',
    ];

    protected array $scopes = ['user_info'];

    protected function getAuthUrl(): string
    {
        // TODO: Implement getAuthUrl() method.
        return $this->buildAuthUrlFromBase($this->getUrl('login/oauth/authorize'));
    }

    protected function getTokenUrl(): string
    {
        // TODO: Implement getTokenUrl() method.
        return $this->getUrl('login/oauth/access_token');
    }

    protected function getUserByToken(string $token): array
    {
        // TODO: Implement getUserByToken() method.
        $response = $this->getHttpClient()->get($this->getUrl('api/v1/user'), [
            RequestOptions::HEADERS => [
                'Authorization' => "token {$token}",
            ],
        ]);

        return $this->fromJsonBody($response);
    }

    protected function mapUserToObject(array $user): Contracts\UserInterface
    {
        // TODO: Implement mapUserToObject() method.
        return new User([
            Contracts\ABNF_ID => $user[Contracts\ABNF_ID] ?? null,
            Contracts\ABNF_NICKNAME => $user['login'] ?? null,
            Contracts\ABNF_NAME => $user[Contracts\ABNF_NAME] ?? null,
            Contracts\ABNF_EMAIL => $user[Contracts\ABNF_EMAIL] ?? null,
            Contracts\ABNF_AVATAR => $user['avatar_url'] ?? null,
        ]);
    }

    protected function getTokenFields(string $code): array
    {
        return array_merge($this->parameters, parent::getTokenFields($code));
    }

    protected function getUrl(string $path): string
    {
        return $this->config->get('instance_uri') . $path;
    }
}
