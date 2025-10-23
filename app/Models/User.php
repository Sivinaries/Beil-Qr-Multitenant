<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }
    
    public function chair()
    {
        return $this->hasMany(Chair::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }
}
