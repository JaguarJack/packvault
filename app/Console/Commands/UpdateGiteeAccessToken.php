<?php

namespace App\Console\Commands;

use App\Enums\Vcs;
use App\Models\VcsPlatform;
use App\Services\OAuth\Gitee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateGiteeAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:gitee:access:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新 Gitee 的 Access Token';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        VcsPlatform::query()
            ->where('vcs', Vcs::GITEE)
            ->whereNotNull('access_token')
            ->get()
            ->each(function (VcsPlatform $app) {
                $gitee = new Gitee();

                $newAccessToken = $gitee->newAccessToken($app->user_id);

                Log::info("更新Gitee token: $newAccessToken");
            });
    }
}
