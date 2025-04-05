<?php
namespace App\Services\Repository;

use App\Exceptions\ComposerJsonInvalidException;

abstract class Repository
{
    protected string $repositoryUrl;

    protected ?string $accessToken = null;

    /**
     * 设置 URL
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): static
    {
        $this->repositoryUrl = $url;

        return $this;
    }

    /**
     * 获取 package 信息
     *
     * @return array
     */
    abstract public function getPackageInfo(): array;


    /**
     * 校验 composer json 文件格式
     *
     * @throws ComposerJsonInvalidException
     */
    protected function validateComposerJson(array $composerJson): void
    {
        if (!isset($composerJson['name'])) {
            throw new ComposerJsonInvalidException('composer.json 缺少 name，不符合 PHP Package 规范');
        }

        $name = $composerJson['name'];

        // Check for vendor/package format
        if (!str_contains($name, '/')) {
            throw new ComposerJsonInvalidException("包名 '{$name}' 必须包含供应商名称和项目名称，并用 '/' 分隔");
        }

        // Validate against the regex pattern
        $pattern = '/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9](([_.]|-{1,2})?[a-z0-9]+)*$/';
        if (!preg_match($pattern, $name)) {
            throw new ComposerJsonInvalidException("包名 '{$name}' 不符合要求的格式。它必须是小写字母，并且只能包含字母数字字符、破折号、下划线和点。");
        }

        if (isset($composerJson['require']) && !is_array($composerJson['require'])) {
            throw new ComposerJsonInvalidException("字段 'require' 必须是一个对象");
        }

        // Check for autoload (must be an object if present)
        if (isset($composerJson['autoload']) && !is_array($composerJson['autoload'])) {
            throw new ComposerJsonInvalidException("字段 'autoload' 必须是一个对象");
        }

        $psrErr = [];
        $this->validateComposerPsr($composerJson, $psrErr);

        if (count($psrErr)) {
            throw new ComposerJsonInvalidException(implode("\n", $psrErr));
        }
    }

    /**
     * 校验 composer psr 规范
     *
     * @param array $data
     * @param $errors
     * @return void
     */
    protected function validateComposerPsr(array $data, &$errors): void
    {
        $autoloadFields = ['autoload', 'autoload-dev'];

        foreach ($autoloadFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            if (!is_array($data[$field])) {
                $errors[] = "字段 '{$field}' 必须是一个对象";
                continue;
            }

            if (isset($data[$field]['psr-4'])) {
                if (!is_array($data[$field]['psr-4'])) {
                    $errors[] = "'{$field}.psr-4' 必须是一个对象";
                } else {
                    foreach ($data[$field]['psr-4'] as $namespace => $path) {
                        if (!str_ends_with($namespace, '\\')) {
                            $errors[] = "PSR-4 命名空间 '{$namespace}' 在 '{$field}' 中必须以命名空间分隔符 '\\' 结尾";
                        }

                        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*\\\\$/', $namespace)) {
                            $errors[] = "PSR-4 命名空间 '{$namespace}' 在 '{$field}' 中格式无效";
                        }

                        if (is_string($path)) {
                            // Validate path format
                            if (empty($path)) {
                                $errors[] = "PSR-4 路径在 '{$field}' 中不能为空";
                            }

                            // 这里还需要每个平台提供一个目录判断 gitee/github/coding, 例如 src/ 目录 todo
                        } elseif (is_array($path)) {
                            foreach ($path as $index => $subPath) {
                                if (!is_string($subPath) || empty($subPath)) {
                                    $errors[] = "PSR-4 路径数组中的项 {$index} 在 '{$field}' 中必须是非空字符串";
                                }
                            }
                        } else {
                            $errors[] = "PSR-4 路径 '{$namespace}' 在 '{$field}' 中必须是字符串或字符串数组";
                        }
                    }
                }
            }

            if (isset($data[$field]['psr-0'])) {
                if (!is_array($data[$field]['psr-0'])) {
                    $errors[] = "'{$field}.psr-0' 必须是一个对象";
                } else {
                    foreach ($data[$field]['psr-0'] as $namespace => $path) {
                        if ($namespace !== '' && !str_ends_with($namespace, '\\')) {
                            $errors[] = "PSR-0 命名空间 '{$namespace}' 在 '{$field}' 中应该以命名空间分隔符 '\\' 结尾";
                        }

                        if (is_string($path)) {
                            if (empty($path)) {
                                $errors[] = "PSR-0 路径在 '{$field}' 中不能为空";
                            }
                        } elseif (is_array($path)) {
                            foreach ($path as $index => $subPath) {
                                if (!is_string($subPath) || empty($subPath)) {
                                    $errors[] = "PSR-0 路径数组中的项 {$index} 在 '{$field}' 中必须是非空字符串";
                                }
                            }
                        } else {
                            $errors[] = "PSR-0 路径 '{$namespace}' 在 '{$field}' 中必须是字符串或字符串数组";
                        }
                    }
                }
            }
        }
    }
}
