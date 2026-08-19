<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'level',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public const LEVEL_ADMIN = 'admin';
    public const LEVEL_STAFF = 'bendahara';

    public function isAdmin()
    {
        return $this->level === self::LEVEL_ADMIN;
    }

    public function isBendahara()
    {
        return $this->level === self::LEVEL_STAFF;
    }
}
