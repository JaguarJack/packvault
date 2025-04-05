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
class GiteeRepository extends Repository
{
    protected string $api = 'https://gitee.com/api/v5';

    /**
     * require name
     *
     * @param string $owner
     * @param string $repoName
     * @return array
     * @throws ComposerJsonInvalidException
     * @throws LostComposerJsonFileException
     */
    protected function getComposerJson(string $owner, string $repoName): array
    {
        $content = $this->getFileContent($owner, $repoName, 'composer.json');

        if (! $content) {
            throw new LostComposerJsonFileException();
        }

        $this->validateComposerJson($content);

        return $content;
    }

    /**
     * 获取文件内容
     *
     * @param string $owner
     * @param string $repoName
     * @param string $path
     * @return array
     * @throws LostComposerJsonFileException
     * @throws Exception
     */
    public function getFileContent(string $owner, string $repoName, string $path): array
    {
        $defaultBranch = $this->getDefaultBranche($owner, $repoName);

        if (! $defaultBranch) {
            throw new Exception('该仓库没有设置分支');
        }

        $url = sprintf($this->api . '/repos/%s/%s/contents/%s', $owner, $repoName, $path);

        $content = base64_decode($this->get($url)['content']);

        if (! $content) {
            throw new LostComposerJsonFileException();
        }

        return json_decode($content, true);
    }

    /**
     * @return array
     * @throws LostComposerJsonFileException
     * @throws ComposerJsonInvalidException
     * @throws Exception
     */
    public function getPackageInfo(): array
    {
        $urlParts = parse_url($this->repositoryUrl);
        $pathParts = explode('/', trim($urlParts['path'], '/'));

        if (count($pathParts) < 2) {
            throw new Exception('无效的 Gitee 仓库 URL地址');
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
     * @param $owner
     * @param $repo
     * @return mixed|null
     * @throws Exception
     */
    public function getDefaultBranche($owner, $repo): mixed
    {
        $branches = $this->get(sprintf($this->api . '/repos/%s/%s/branches', $owner, $repo), [
            'per_page' => 100
        ]);

        return $branches[0]['name'] ?? null;
    }


    /**
     * @param string $url
     * @param array $query
     * @return array
     * @throws Exception
     */
    protected function get(string $url, array $query = []): array
    {
        $response = Http::get($url, array_merge($query, [
            'access_token' => $this->getAccessToken(),
        ]));

        $data = $response->json();

        if (! $response->successful()) {
            throw new Exception("Gitee 接口查询失败: {$data['message']}");
        }

        return $data;
    }


    public function getAccessToken()
    {
        if ($this->accessToken === null) {
            $this->accessToken = Str::of(VcsPlatform::getUserGiteeAccessToken())->explode('@')->get(1);
        }

        return $this->accessToken;
    }
}
