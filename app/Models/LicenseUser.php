<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property $team_id
 * @property $id
 * @property $license_id
 * @property $email
 * @property $license
 * @property $allow_ip_number
 * @property $ip_address
 * @property $status
 */
class LicenseUser extends Model
{
    //
    protected $fillable = [
        'team_id',
        'license_id',
        'email',
        'allow_ip_number',
        'ip_address',
        'status',
        'license'
    ];

    /**
     * 状态
     *
     * @return Attribute
     */
    public function status():Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool) $value,
        );
    }

    public function isForbidden():bool
    {
        return ! $this->status;
    }

    /**
     * 是否有剩余可访问的ip地址
     *
     * @return bool
     */
    public function hasLeftAllowedIpNumber():bool
    {
        if (! $this->ip_address) {
            return true;
        }

        return $this->allow_ip_number > count(json_decode($this->ip_address, true));
    }

    /**
     * 保存 IP
     *
     * @param string $ip
     * @return bool
     */
    public function saveIp(string $ip): bool
    {
        $ips = [];

        if ($this->ip_address) {
            $ips = json_decode($this->ip_address, true);
        }

        if (in_array($ip, $ips)) {
            return true;
        }

        $ips[] = $ip;
        $this->ip_address = json_encode($ips);
        return $this->save();
    }
}
