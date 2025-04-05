<?php
return [
    /**
     * Github 授权
     */
    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => env('GITHUB_CALLBACK'),
        'scopes'        => ['read:user', 'repo']
    ],

    /**
     * Gitee 授权
     */
    'gitee' => [
        'client_id'     => env('GITEE_CLIENT_ID'),
        'client_secret' => env('GITEE_CLIENT_SECERT'),
        'redirect'      => env('GITEE_CALLBACK'),
    ],

    'gitea' => [
        'client_id'     => env('GITEA_CLIENT_ID'),
        'client_secret' => env('GITEA_CLIENT_SECERT'),
        'redirect'      => env('GITEA_CALLBACK'),
        'instance_uri'  => env('GITEA_INSTANCE_URI'),
    ]
];
