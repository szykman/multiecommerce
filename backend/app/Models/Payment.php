<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'reference',
        'amount',
        'status',
        'raw_response',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
