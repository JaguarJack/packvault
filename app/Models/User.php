<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property $id
 * @property $name
 * @property $email
 * @property $password
 * @property $experience_at
 * @property $created_at
 * @property $email_verified_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'experience_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'date:Y-m-d H:i:s',
            'password' => 'hashed',
            'created_at' => 'date:Y-m-d H:i:s',
            'updated_at' => 'date:Y-m-d H:i:s',
            'experience_at' => 'date:Y-m-d H:i:s',
        ];
    }

    /**
     * 体验是否到期
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        // 如果体验期是 null
        if (! $this->experience_at) {
            return false;
        }

        // 体验期小于当前时间
        if ($this->experience_at >= date('Y-m-d H:i:s')) {
            return false;
        }

        return true;
    }
}
