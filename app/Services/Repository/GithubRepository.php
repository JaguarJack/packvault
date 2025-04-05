<?php
namespace App\Services\Repository;

use App\Exceptions\ComposerJsonInvalidException;
use App\Exceptions\LostComposerJsonFileException;
use App\Models\VcsPlatform;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * github 仓库
 */
class GithubRepository extends Repository
{
    protected string $api = 'https://api.github.com';

    /**
     * @param $owner
     * @param $repo
     * @return array|mixed
     * @throws Exception
     */
    protected function getRepositoryInfo($owner, $repo): mixed
    {
        return $this->get($this->api . "/repos/$owner/$repo");
    }

    /**
     * @param $owner
     * @param $repo
     * @return mixed
     * @throws ComposerJsonInvalidException
     * @throws LostComposerJsonFileException
     * @throws \Throwable
     */
    protected function getComposerJson($owner, $repo): mixed
    {
        $content = $this->getFileContent($owner, $repo, 'composer.json');

        $content = json_decode(base64_decode($content['content']), true);

        $this->validateComposerJson($content);

        return $content;
    }

    /**
     * @param $owner
     * @param $repo
     * @param $path
     * @return array|mixed
     * @throws ComposerJsonInvalidException
     * @throws LostComposerJsonFileException
     * @throws \Throwable
     */
    public function getFileContent($owner, $repo, $path): mixed
    {
        try {
            $repo = $this->getRepositoryInfo($owner, $repo);

            return $this->get("{$repo['contents_url']}/{$path}");
        } catch (\Throwable $exception) {
            if (Str::of($exception->getMessage())->lower()->contains('not found')) {
                throw new LostComposerJsonFileException("{$path} 文件未找到");
            }

            throw $exception;
        }
    }

    /**
     * @return array
     * @throws ComposerJsonInvalidException
     * @throws LostComposerJsonFileException
     * @throws \Throwable
     */
    public function getPackageInfo(): array
    {
        // TODO: Implement getPackageInfo() method.
        $urlParts = parse_url($this->repositoryUrl);
        $pathParts = explode('/', trim($urlParts['path'], '/'));

        if (count($pathParts) < 2) {
            throw new Exception('无效的 Github 仓库 URL地址');
        }

        [$owner, $repo] = $pathParts;

        $composerJson = $this->getComposerJson($owner, $repo);

        return [
            'name' => $composerJson['name'],
            'description' => $composerJson['description'] ?? null,
            'type' => $composerJson['type'] ?? 'library',
            'repo_name' => "{$owner}/{$repo}"
        ];
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    protected function get(string $url)
    {
        $response = Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Accept' => 'application/vnd.github.v3+json'
            ])->get($url);

        $data = $response->json();
        if (! $response->successful()) {
            throw new Exception('Github Api 请求错误: ' . $data['message']);
        }

        return $response->json();
    }

    /**
     * @return mixed|string|null
     */
    protected function getAccessToken(): mixed
    {
        if ($this->accessToken === null) {
            $this->accessToken = VcsPlatform::getUserGithubAccessToken();
        }

        return $this->accessToken;
    }
}
