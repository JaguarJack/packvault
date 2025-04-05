<?php
namespace App\Jobs;

use App\Services\PackageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RemovePackageJsonSourceJob implements ShouldQueue
{

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
    )
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $packageService = new PackageService();

        $packageService->scanAllPackageJsonFiles();
    }
}
