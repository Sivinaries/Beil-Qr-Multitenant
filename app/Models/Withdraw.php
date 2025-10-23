<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'payment_type',
        'amount',
        'no_rek',
        'settled_at',
        'note',
        'status',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
