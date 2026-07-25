<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Cliente final da loja (comprador). Usa o guard "customer" (ver
 * config/auth.php), separado do guard "web" usado pelos usuários
 * administrativos (super user / reseller / store manager).
 *
 * Importante: este model NÃO usa o StoreScope global (que filtra
 * por auth()->user()->store_id do guard admin) — o escopo por loja
 * aqui é feito explicitamente nos controllers via TenantManager,
 * já que um Customer é autenticado no contexto do domínio/tenant
 * público que ele está visitando, não de uma sessão administrativa.
 */
class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'phone',
        'document',
        'password',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
