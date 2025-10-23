<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'user_id',
        'name',
        'no_telpon',
        'ktp',
        'atas_nama',
        'bank',
        'no_rek',
        'store',
        'status',
        'location',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function showcases()
    {
        return $this->hasMany(Showcase::class);
    }

    public function chairs()
    {
        return $this->hasMany(Chair::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    public function histories()
    {
        return $this->hasMany(Histoy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
