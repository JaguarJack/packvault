<?php

namespace App\Jobs;

use App\Events\PackageBuildNotification;
use App\Events\PackageBuildOutput;
use App\Models\BuildPackageJob;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\ExecutableFinder;

class PackageBuildJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Package         $package,
        protected BuildPackageJob $buildPackageJob,
        protected string          $accessToken
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $packageService = new PackageService();

        try {
            $this->buildPackageJob->running();

            chmod($packageService->getPath(), 0777);

            $packageService->createAuthJson($this->accessToken, $this->package->stay_at);

            $packageService->createComposerJson();

            $finder = new ExecutableFinder();

            if (!is_dir($packagePath = $packageService->getPath())) {
                mkdir($packagePath, 0777, true);
            }

            $process = Process::path($packagePath)
                        ->command([
                            $finder->find('php'),
                            $this->getSatisExecutable(),
                            'build',
                            'satis.json',
                            '--no-html-output',
                            '--repository-url=' . $this->package->getUrl()
                        ])
                        ->timeout(3000)
                        ->start();

            $currentContent = '';
            $result = $process->waitUntil(function (string $type, string $output) use (&$currentContent) {
                if ($type == 'out') {
                    $currentContent .= $output;
                    $this->broadcastOutputMessage($currentContent);
                }
            });

            if ($result->successful()) {
                // 通知用户成功
                $this->buildJobSuccess("{$this->package->name} 构建成功");
            } else {
                // 通知用户失败
                $this->broadcastOutputMessage('over');
                $this->buildJobFailed("{$this->package->name} 构建失败:  ". $result->errorOutput());
            }
        } catch (\Throwable|\Exception $exception) {
            Log::info("{$this->package->name} 构建失败:  ". $exception->getMessage());
            $this->buildJobFailed("{$this->package->name} 构建失败:  ". $exception->getMessage());
        } finally {
            $this->broadcastOutputMessage('over');
            $packageService->cleanTempComposerCache();
        }
    }

    /**
     * 获取 satis 执行
     *
     * @return string
     */
    protected function getSatisExecutable(): string
    {
        return base_path('vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'satis');
    }

    /**
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->buildPackageJob->id;
    }

    /**
     * @param string $message
     * @return void
     */
    protected function buildJobSuccess(string $message): void
    {
        $this->buildPackageJob->success($message);
        $this->broadcastBuildNotificationMessage($message);
        // 同步 tag
        SyncPackageTagsJob::dispatch($this->package, $this->buildPackageJob);
    }

    /**
     * @param string $message
     * @return void
     */
    protected function buildJobFailed(string $message): void
    {
        $this->buildPackageJob->failed($message);
        $this->broadcastBuildNotificationMessage($message);
    }

    /**
     * 广播进程输出信息
     *
     * @param string $message
     * @return void
     */
    protected function broadcastOutputMessage(string $message): void
    {
        if (config('packvault.is_use_broadcast')) {
            event(new PackageBuildOutput($message, $this->package->id));
        }
    }

    /**
     * 广播通知
     *
     * @param string $message
     * @return void
     */
    protected function broadcastBuildNotificationMessage(string $message): void
    {
        if (config('packvault.is_use_broadcast')) {
            event(new PackageBuildNotification($message, $this->package->user_id));
        }
    }
}
