<?php
namespace App\Enums;

/**
 * 第三方服务
 */
enum Vcs : string {
    case GITHUB = 'github';
    case GITEE = 'gitee';
    case GITLAB = 'gitlab';
    case GITEA = 'gitea';
    case CODING = 'coding';


    public static function getServices(): array
    {
        $services = [];
        $privateConfig = config('packvault.vcs');

        if ($privateConfig['is_support_github']) {
            $services[] = [
                'label' => 'GitHub',
                'value' => self::GITHUB->value,
            ];
        }

        if ($privateConfig['is_support_gitee']) {
            $services[] = [
                'label' => 'Gitee',
                'value' => self::GITEE->value,
            ];
        }

        if ($privateConfig['is_support_gitlab']) {
            $services[] = [
                'label' => 'GitLab',
                'value' => self::GITLAB->value,
            ];
        }

        if ($privateConfig['is_support_gitea']) {
            $services[] = [
                'label' => 'Gitea',
                'value' => self::GITEA->value,
            ];
        }

        if ($privateConfig['is_support_coding']) {
            $services[] = [
                'label' => 'Coding',
                'value' => self::CODING->value,
            ];
        }

        return $services;
    }
}
