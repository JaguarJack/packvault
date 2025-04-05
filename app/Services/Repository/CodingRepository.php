<?php
namespace App\Services\Repository;

use App\Exceptions\ComposerJsonInvalidException;
use App\Exceptions\LostComposerJsonFileException;
use App\Models\VcsPlatform;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * coding 仓库
 */
class CodingRepository extends Repository
{
    protected string $api = 'https://e.coding.net/open-api';



    public function __construct()
    {}


    /**
     * 获取项目路径
     *
     * @return string|null
     */
    protected function getDepotPath(): ?string
    {
        $pattern = '/https?:\/\/(?:e\.)?coding\.net\/([^\/]+)\/([^\/]+)\/([^\/\.]+)(?:\.git)?/';

        if (preg_match($pattern, $this->repositoryUrl, $matches)) {
            return implode('/', [
                'team' => $matches[1],
                'project' => $matches[2],
                'repository' => $matches[3]
            ]);
        }

        return null;
    }


    /**
     * @throws ConnectionException
     * @throws LostComposerJsonFileException|ComposerJsonInvalidException
     */
    public function getPackageInfo(): array
    {
        $depotPath = $this->getDepotPath();

        $ref = $this->getDefaultBranch($depotPath);

        if (! $this->isComposerJsonExists($depotPath, $ref)) {
            throw new LostComposerJsonFileException('仓库缺少 composer.json，不符合 PHP package 规范');
        }

        $composerJson = $this->getComposerJson($depotPath, $ref);

        $this->validateComposerJson($composerJson);

        return [
            'name' => $composerJson['name'],
            'description' => $composerJson['description'] ?? null,
            'type' => $composerJson['type'] ?? 'library',
            'repo_name' => $depotPath,
        ];
    }


    /**
     * require name
     *
     * @param string $depotPath
     * @param $ref
     * @return false|mixed|null
     * @throws ConnectionException
     */
    protected function getComposerJson(string $depotPath, $ref): mixed
    {
        $data = $this->post([
            'Action' => 'DescribeGitFile',
            'DepotPath' => $depotPath,
            'Path' => 'composer.json',
            'Ref' => $ref
        ]);

        if ($data) {
            $content = base64_decode($data['GitFile']['Content']);
            return json_decode($content, true);
        }

        return false;
    }

    /**
     * composer json
     *
     * @param string $depotPath
     * @param $ref
     * @return bool
     * @throws ConnectionException
     */
    public function isComposerJsonExists(string $depotPath, $ref): bool
    {
        $data = $this->post([
            'Action' => 'DescribeGitFileStat',
            'DepotPath' => $depotPath,
            'Path' => 'composer.json',
            'Ref' => $ref
        ]);

        return $data['Payload']['IsExist'] ?? false;
    }


    protected function getDefaultBranch($path): mixed
    {
        $data = $this->post([
            'Action' => 'DescribeDepotDefaultBranch',
            'DepotPath' => $path,
        ]);

        if ($data) {
            return $data['BranchName'];
        }

        return $data;
    }

    /**
     * @param array $body
     * @return false
     * @throws ConnectionException
     * @throws \Exception
     */
    protected function post(array $body)
    {
        $accessToken = Str::of(VcsPlatform::getUserCodingAccessToken());

        $isProject = $accessToken->contains('@');

        if ($this->accessToken === null) {
            $this->accessToken = !$isProject ? $accessToken->toString() : $accessToken->replace('@', ':')->toBase64()->toString();
        }

        $token = sprintf($isProject? 'Basic %s' : 'token %s',  $this->accessToken);
        $response = Http::withHeader('Authorization', $token)->post($this->api, $body);

        if (! $response->successful()) {
            return false;
        }

        $responseData = $response->json('Response');

        if (isset($responseData['Error'])) {
            throw new \Exception($responseData['Error']['Message']);
        }

        return $response->json('Response');
    }
}
