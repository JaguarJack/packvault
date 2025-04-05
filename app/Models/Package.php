<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property $id
 * @property $team_id
 * @property $name
 * @property $repo_name
 * @property $app_id
 * @property $user_id
 * @property $url
 * @property $stay_at
 * @property $status
 * @property $description
 */
class Package extends Model
{
    //
    protected $fillable = [
        'team_id',
        'name',
        'repo_name',
        'vcs_id',
        'user_id',
        'url',
        'stay_at',
        'status',
        'description',
    ];

    public function tags(): HasMany
    {
        return $this->hasMany(PackageTag::class, 'package_id', 'id');
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool) $value,
            set: fn($value) => (int) $value
        );
    }

    /**
     * license 授权的包
     *
     * @return BelongsToMany
     */
    public function licenses(): BelongsToMany
    {
        return $this->belongsToMany(License::class, LicensePackage::class, 'package_id', 'license_id');
    }

    /**
     * @param int $userId
     * @param string[] $columns
     * @return Collection
     */
    public static function getCurrenUserAvailablePackages(int $userId, array $columns = ['*']): Collection
    {
        return self::query()->where('user_id', $userId)
                        ->where('status', 1)
                        ->get($columns);
    }

    public function getUrl()
    {
        return $this->url;
    }
}
