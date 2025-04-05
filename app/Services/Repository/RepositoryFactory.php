<?php
namespace App\Services\Repository;

use App\Enums\Vcs;

class RepositoryFactory
{
    /**
     * @param string $vcs
     * @return Repository
     * @throws \Exception
     */
    public static function make(string $vcs): Repository
    {
        return match ($vcs) {
            Vcs::CODING->value => new CodingRepository(),
            Vcs::GITEE->value => new GiteeRepository(),
            Vcs::GITHUB->value => new GithubRepository(),
            default => throw new \Exception("不支持该 {$vcs} 平台")
        };
    }
}
