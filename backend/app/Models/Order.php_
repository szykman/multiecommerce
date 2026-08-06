<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'store_id',
        'customer_id',
        'customer_address_id',
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'address_snapshot',
    ];

    protected $casts = [
        'address_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'customer_address_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Aguardando pagamento',
            'awaiting_confirmation' => 'Aguardando confirmação da loja',
            'paid' => 'Pago',
            'cancelled' => 'Cancelado',
            default => ucfirst($this->status),
        };
    }
}
