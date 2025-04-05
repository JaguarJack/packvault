<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property $id
 * @property $package_id
 * @property $name
 * @property $description
 * @property $type
 * @property $dist
 * @property $download_times
 * @property $require
 * @property $authors
 * @property $created_at
 * @property $updated_at
 *
 */
class PackageTag extends Model
{
    protected $table = 'package_tags';

    //
    protected $fillable = [
        'package_id',
        'name',
        'description',
        'type',
        'dist',
        'download_times',
        'require',
        'authors',
        'created_at',
        'updated_at'
    ];


    public function require(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value)
        );
    }


    protected function authors(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value)
        );
    }
}
