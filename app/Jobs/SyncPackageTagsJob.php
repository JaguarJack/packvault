<?php
namespace App\Jobs;

use App\Models\PackageTag;
use App\Models\Team;
use App\Models\BuildPackageJob;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncPackageTagsJob implements ShouldQueue, ShouldBeUnique
{

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Package         $package,
        protected BuildPackageJob $buildPackageJob,
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

        $package = $packageService->getPackage($this->package->name);
        Log::info(json_encode($package));
        $tags = PackageTag::query()->where('package_id', $this->package->id)
                        ->get(['id','name', 'dist'])
                        ->keyBy('name')
                        ->toArray();

        if (! empty($package)) {
            foreach ($package as $packageVersion) {
                if (! isset($tags[$packageVersion['name']])) {
                    $packageVersion['package_id'] = $this->package->id;
                    $packageTas = new PackageTag($packageVersion);
                    $packageTas->save();
                } else {
                    if ($packageVersion['dist'] != $tags[$packageVersion['name']]['dist']) {
                        PackageTag::query()
                            ->where('id', $tags[$packageVersion['name']]['id'])
                            ->update([
                                'dist' => $packageVersion['dist'],
                                'updated_at' => $packageVersion['updated_at']
                            ]);
                    }
                }
            }
        }

        // 去除 package json source 信息
        RemovePackageJsonSourceJob::dispatch();
    }

    /**
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->package->name;
    }
}
