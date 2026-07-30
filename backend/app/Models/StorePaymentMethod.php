<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePaymentMethod extends Model
{
    protected $fillable = [
        'store_id',
        'provider',
        'enabled',
        'credentials',
        'position',
    ];

    protected $casts = [
        'enabled' => 'boolean',

        // 'encrypted:array' faz o Laravel criptografar/descriptografar
        // automaticamente usando a APP_KEY — nunca fica texto puro
        // no banco, e no PHP você lê/escreve como array normal, sem
        // precisar chamar Crypt::encrypt()/decrypt() manualmente.
        'credentials' => 'encrypted:array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Rótulo amigável do provedor, para exibir no admin/checkout.
     */
    public function getLabelAttribute(): string
    {
        return match ($this->provider) {
            'pix_manual' => 'PIX (chave manual)',
            'mercadopago' => 'Mercado Pago',
            'pagseguro' => 'PagSeguro',
            'stripe' => 'Stripe',
            default => ucfirst($this->provider),
        };
    }
}
