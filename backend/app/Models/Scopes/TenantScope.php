<?php

namespace App\Models\Scopes;

use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra automaticamente por store_id com base no tenant ATUAL
 * (o domínio público sendo visitado, via TenantManager) — diferente
 * da StoreScope usada no admin, que filtra por auth()->user()->store_id
 * do guard "web" (sessão administrativa).
 *
 * Usar nos models do lado do cliente/loja pública: Customer, e
 * futuramente Order, Address, Wishlist, Review — nunca nos models
 * administrados pelo painel admin (Product, Category etc.), que já
 * têm sua própria lógica de escopo.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $storeId = app(TenantManager::class)->id();

        if ($storeId) {
            $builder->where($model->getTable().'.store_id', $storeId);
        }
    }
}
