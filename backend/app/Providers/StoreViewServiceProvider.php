<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\StoreSetting;
use App\Services\TenantManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Centraliza tudo que precisa estar disponível automaticamente em
 * qualquer view do storefront (resources/views/store/*) — loja
 * atual, configurações, categorias, contador do carrinho, etc.
 *
 * Isso evita que cada controller novo (Checkout, Wishlist, Reviews,
 * CMS...) precise lembrar de passar essas variáveis manualmente —
 * e evita o erro "Undefined variable $settings" que já tivemos
 * quando um controller esquecia de passar alguma delas.
 */
class StoreViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('store.*', function ($view) {

            $tenant = app(TenantManager::class);

            $store = $tenant->getStore();

            $cart = session()->get('cart', []);

            $cartCount = collect($cart)->sum('qty');

            $settings = null;
            $categories = collect();
            $cmsCategories = collect();

            if ($store) {

                $settings = StoreSetting::firstOrCreate([
                    'store_id' => $store->id,
                ]);

                $categories = Category::where('store_id', $store->id)
                    ->where('active', 1)
                    ->where('type', 'store')
                    ->orderBy('name')
                    ->get();

                $cmsCategories = Category::where('store_id', $store->id)
                    ->where('active', 1)
                    ->where('type', 'cms')
                    ->with(['products' => function ($q) {
                        $q->where('active', 1)->orderBy('name');
                    }])
                    ->orderBy('name')
                    ->get();
            }

            $view->with([
                'store' => $store,
                'settings' => $settings,
                'categories' => $categories,
                'cmsCategories' => $cmsCategories,
                'cartCount' => $cartCount,
            ]);
        });
    }
}
