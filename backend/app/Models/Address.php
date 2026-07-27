<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_id',
        'store_id',
        'label',
        'recipient_name',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFullAddressAttribute(): string
    {
        $line = "{$this->street}, {$this->number}";

        if ($this->complement) {
            $line .= " - {$this->complement}";
        }

        $line .= " - {$this->neighborhood}, {$this->city}/{$this->state}";
        $line .= " - CEP {$this->zipcode}";

        return $line;
    }
}
