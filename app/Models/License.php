<?php

namespace App\Models;

use App\Enums\SubscribeType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property $team_id
 * @property $title
 * @property $license
 * @property $type
 * @property $user_id
 * @property $status
 * @property $expired_at
 */
class License extends Model
{
    //
    protected $fillable = [
        'team_id',
        'title',
        'license',
        'type',
        'user_id',
        'status',
        'expired_at',
    ];

    /**
     * license 授权的包
     *
     * @return BelongsToMany
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'license_packages', 'license_id', 'package_id');
    }

    protected function expiredAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('Y-m-d H:i',$value)
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
        );
    }

    public static function types(): array
    {
        return [
            SubscribeType::YEAR->value => SubscribeType::YEAR->label(),
            SubscribeType::FOREVER->value => SubscribeType::FOREVER->label(),
        ];
    }

    public function isExpired(): bool
    {
        return $this->expired_at < time();
    }
}
