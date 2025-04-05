<?php
namespace App\Services;

use App\Enums\Vcs;
use App\Support\Helper;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PackageService
{
    protected string $path;

    public function __construct()
    {
        $this->path = $this->createPath();
    }

    /**
     * 存储路径
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * @return string
     */
    protected function createPath(): string
    {
        $path = config('packvault.path');

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * @param string $packageName
     * @return array|mixed
     * @throws Exception
     */
    public function getPackage(string $packageName): mixed
    {
        $packages = [];

        $jsonFile = $this->getPackageIndexJson($packageName);

        if (! $jsonFile) {
            throw new Exception("$packageName 的元信息 JSON 不存在");
        }

        $json = file_get_contents($jsonFile);

        if (! Str::of($json)->isJson()) {
            throw new Exception("$packageName 的元信息非 JSON");
        }

        $versions = json_decode($json, true)['packages'][$packageName];

        foreach ($versions as $version) {
            $packages[] = [
                'name' => $version['version'],
                'require' => $version['require'] ?? '',
                'dist' => pathinfo($version['dist']['url'], PATHINFO_BASENAME),
                'type' => $version['type'],
                'authors' => $version['authors'] ?? [],
                'description' => $version['description'] ?? '',
                'updated_at' => date('Y-m-d H:i:s', strtotime($version['time'])),
            ];
        }

        return $packages;
    }

    /**
     * @return string
     */
    public function getPackageDistPath(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . 'dist';
    }

    /**
     * @return mixed
     */
    public function getPackageJsonContent(): mixed
    {
        return json_decode(file_get_contents($this->getPackageDistPath() . DIRECTORY_SEPARATOR . 'packages.json'), true);
    }

    /**
     * @return string
     */
    public function getPackagePath(): string
    {
        return $this->getPackageDistPath() . DIRECTORY_SEPARATOR . 'p';
    }

    /**
     * 扫描 package json 文件，去除 source
     *
     * @return void
     */
    public function scanAllPackageJsonFiles(): void
    {
        $packageJson = $this->getPackageJsonContent();

        $p = [];
        foreach ($packageJson['providers'] as $key => $package) {
            $path = explode('/', $key);
            $p[$path[0]][] = $path[1];
        }

        foreach ($p as $path => $names) {
            $files = File::allFiles($this->getPackagePath(). DIRECTORY_SEPARATOR . $path);
            foreach ($files as $file) {
                if ($file->isWritable()) {
                    $content = $this->removePackageJsonSourceInfo(json_decode($file->getContents(), true));
                    $content && File::put($file->getRealPath(), $content);
                }
            }
        }

        foreach ($packageJson['includes'] as $key => $package) {
            $path = explode('/', $key);
            $files = File::allFiles($this->getPackageDistPath() . DIRECTORY_SEPARATOR . $path[0]);
            foreach ($files as $file) {
                if ($file->isWritable()) {
                    $content = $this->removePackageJsonSourceInfo(json_decode($file->getContents(), true));
                    $content && File::put($file->getRealPath(), $content);
                }
            }
        }
    }

    /**
     * 移除 source 里面的信息
     *
     * @param $data
     * @return false|string
     */
    protected function removePackageJsonSourceInfo($data): bool|string
    {
        $isHasSource = false;

        if (isset($data['packages']) && is_array($data['packages'])) {
            foreach ($data['packages'] as $packageName => $versions) {
                foreach ($versions as $version => $packageInfo) {
                    if (isset($packageInfo['source'])) {
                        $isHasSource = true;
                        unset($data['packages'][$packageName][$version]['source']);
                    }

                    // 同时也将 installation-source 从 source 改为 dist
                    if (isset($packageInfo['installation-source']) && $packageInfo['installation-source'] === 'source') {
                        $data['packages'][$packageName][$version]['installation-source'] = 'dist';
                    }
                }
            }
        }

        if ($isHasSource) {
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        }

        return false;
    }


    /**
     * 获取包的索引 index
     *
     * @param string $packageName
     * @return mixed
     */
    protected function getPackageIndexJson(string $packageName): mixed
    {
        [$first, $second ] = explode('/', $packageName);

        $files = File::allFiles($this->path .
            DIRECTORY_SEPARATOR . 'dist' .
            DIRECTORY_SEPARATOR . 'p' .
            DIRECTORY_SEPARATOR . $first . DIRECTORY_SEPARATOR);

        foreach ($files as $file) {
            if ($file->getExtension() === 'json' &&
                Str::of($file->getFilename())->startsWith("$second$")
            ) {
                return $file->getRealPath();
            }
        }

        return null;
    }


    /**
     * 获取 auth json
     *
     * @param string $token
     * @param string $type
     * @return bool|int
     * @throws ConnectionException
     */
    public function createAuthJson(string $token, string $type): bool|int
    {
        return match ($type) {
            Vcs::GITHUB->value => $this->createGithubAuthJson($token),
            Vcs::GITEE->value => $this->createGiteeAuthJson($token),
            Vcs::CODING->value => $this->createCodingAuthJson($token),
        };
    }


    /**
     * 创建 github auth json
     *
     * @param string $token
     * @return bool|int
     */
    protected function createGithubAuthJson(string $token): bool|int
    {
        $authJsonFile = $this->path . DIRECTORY_SEPARATOR . 'auth.json';

        if (! file_exists($authJsonFile)) {
            $authJson = [
                'github-oauth' => [
                    'github.com' => $token
                ]
            ];

        } else {
            $authJson = json_decode(file_get_contents($authJsonFile), true);

            $authJson['github-oauth']['github.com'] = $token;
        }

        return file_put_contents($authJsonFile, json_encode($authJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

    /**
     * 创建 Gitee auth json
     *
     * @param string $token
     * @return bool|int
     */
    protected function createGiteeAuthJson(string $token): bool|int
    {
        $authJsonFile = $this->getAuthJson();

        [$username, $token] = explode('@', $token);

        if (! file_exists($authJsonFile)) {
            $authJson = [
                'http-basic' => [
                    'gitee.com' => [
                        'username' => $username,
                        'password' => $token
                    ]
                ]
            ];

        } else {
            $authJson = json_decode(file_get_contents($authJsonFile), true);

            $authJson['http-basic']['gitee.com'] = [
                'username' => $username,
                'password' => $token
            ];
        }

        return file_put_contents($authJsonFile, json_encode($authJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

    /**
     * @return string
     */
    public function createTempComposerCache(): string
    {
        if (! is_dir($tempCacheDir = $this->tempComposerCache())) {
            mkdir($tempCacheDir, 0755, true);
        }

        return $tempCacheDir;
    }


    /**
     * 清理目录
     *
     * @return void
     */
    public function cleanTempComposerCache(): void
    {
        File::deleteDirectory($this->tempComposerCache(), true);
    }


    /**
     * 临时 composer cache 目录
     *
     * @return string
     */
    public function tempComposerCache(): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . 'temp_composer_cache';
    }

    /**
     * auth json file
     *
     * @return string
     */
    public function getAuthJson(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . 'auth.json';
    }

    /**
     * @throws ConnectionException
     */
    protected function createCodingAuthJson(string $token): bool|int
    {
        $authJsonFile = $this->path . DIRECTORY_SEPARATOR . 'auth.json';

        [$username, $token] = explode('@', $token);

        if (! file_exists($authJsonFile)) {
            $authJson = [
                'http-basic' => [
                    'e.coding.net' => [
                        'username' => $username,
                        'password' => $token
                    ]
                ]
            ];
        } else {
            $authJson = json_decode(file_get_contents($authJsonFile), true);

            $authJson['http-basic']['e.coding.net'] = [
                'username' => $username,
                'password' => $token
            ];
        }

        return file_put_contents($authJsonFile, json_encode($authJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param string $token
     * @return string
     * @throws ConnectionException
     */
    protected function getCodingUserName(string $token): string
    {
        return Http::withHeader('Authorization', 'token ' . $token)
            ->post('https://e.coding.net/open-api', [
                'Action' => 'DescribeCodingCurrentUser'
            ])->json('Response')['User']['Name'];
    }

    /**
     * @param string $token
     * @return string
     */
    protected function getGiteeUserName(string $token): string
    {
        return Http::get('https://gitee.com/api/v5/user', ['access_token' => $token])->json('name');
    }

    /**
     * @param string $teamName
     * @param array $repositories
     * @return string
     */
    public function createSatisJson(array $repositories):string
    {
        $satisJson = $this->getSatisJsonPath();

        $_repositories = [];
        foreach ($repositories as $repository) {
            $_repositories[] = [
                'type' => 'vcs',
                'url' => $repository['url']
            ];
        }

        $satis = [
            'name' => 'private/package',
            'homepage' => config('packvault.domain'),
            'output-dir' => 'dist',
            'repositories' => $_repositories,
            'archive' => [
                'directory' => 'dist',
                'skip-dev' => false
            ],
            'require-all' => true,
            'providers' => true,
            'output-html' => false
        ];

        file_put_contents($satisJson, json_encode($satis, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

        return $satisJson;
    }

    /**
     * @return string
     */
    public function getSatisJsonPath(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . 'satis.json';
    }


    /**
     * 创建 composer json
     *
     * @return bool|int
     */
    public function createComposerJson(): bool|int
    {
        $defaultComposerJson = [
            'require-dev' => [
                'composer/satis' => 'dev-main'
            ],
            'config' => [
                'allow-plugins' => [
                    'composer/satis' => true
                ]
            ]
        ];

        return file_put_contents(
            $this->path . DIRECTORY_SEPARATOR . 'composer.json',
            json_encode($defaultComposerJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES
        ));
    }
}
