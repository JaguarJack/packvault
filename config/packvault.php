<?php
return [
    // 私有包存库域名
    'domain' => env('PACKVAULT_DOMAIN'),

    // 私有存储目录
    'path' => storage_path('packvault'),

    // 使用广播
    'is_use_broadcast' => env('PACKVAULT_USE_BROADCAST', false),

    // vcs 支持平台
    'vcs' => [
        'is_support_github' => env('IS_SUPPORT_GITHUB', true),

        'is_support_gitee' => env('IS_SUPPORT_GITEE', false),

        'is_support_coding' => env('IS_SUPPORT_CODING', false),

        'is_support_gitlab' => env('IS_SUPPORT_GITLAB', false),

        'is_support_gitea' => env('IS_SUPPORT_GITEA', false),
    ]
];
