<?php

namespace App\Models;

use App\Enums\BuildJobStatus;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property $id
 * @property $package_id
 * @property $package_name
 * @property $user_id
 * @property $tag
 * @property $status
 * @property $output
 *
 */
class BuildPackageJob extends Model
{
    //
    protected $fillable = [
        'id',
        'package_id',
        'package_name',
        'user_id',
        'tag',
        'status',
        'output'
    ];

    /**
     * @param int $packageId
     * @param int $userId
     * @param string $tag
     * @return self
     */
    public static function createJob(int $packageId, string $packageName, int $userId, string $tag): static
    {
        $self = new self();
        $self->package_id = $packageId;
        $self->package_name = $packageName;
        $self->user_id = $userId;
        $self->tag = $tag;
        $self->status = BuildJobStatus::UN_START->value;
        $self->save();
        return $self;
    }

    public function running()
    {
        $this->status = BuildJobStatus::RUNNING->value;

        $this->save();
    }

    public function success(string $output)
    {
        $this->status = BuildJobStatus::SUCCESS->value;

        $this->output = $output;

        $this->save();
    }

    public function failed(string $output)
    {
        $this->status = BuildJobStatus::FAILED->value;

        $this->output = $output;

        $this->save();
    }

    /**
     * 是否有正在运行的任务
     *
     * @param $packageId
     * @return bool
     */
    public static function hasUnstartOrRunning($packageId): bool
    {
        return self::query()->where('package_id', $packageId)
                            ->whereIn('status', [BuildJobStatus::UN_START->value, BuildJobStatus::RUNNING->value])
                            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-15 minutes')))
                            ->exists();
    }

    /**
     * @return HasOne
     */
    public function package(): HasOne
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }
}
