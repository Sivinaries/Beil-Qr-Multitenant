<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Histoy extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id',
        'kursi',
        'name',
        'no_order',
        'order',
        'payment_type',
        'total_amount',
        'status',
        'settlement_id',
    ];

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}
