<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $team_id
 * @property $package_id
 * @property $user_id
 * @property $package_name
 * @property $version
 * @property $ip
 * @property $source
 */
class PackageDownload extends Model
{
    //
    protected $table = 'package_download';

    protected $fillable = [
        'package_id',
        'user_id',
        'package_name',
        'version',
        'ip',
        'source'
    ];
}
