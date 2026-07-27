<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

$this->app->singleton(
    \App\Services\TenantManager::class,
    function () {
        return new \App\Services\TenantManager();
    }
);
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    View::composer('store.*', function($view){

        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();

        $cart = session()->get('cart', []);

        $cartCount = collect($cart)->sum('qty');

        $settings = null;
        $categories = collect();
        $cmsCategories = collect();

        if ($store) {

            $settings = \App\Models\StoreSetting::firstOrCreate([
                'store_id' => $store->id
            ]);

            $categories = \App\Models\Category::where('store_id', $store->id)
                ->where('active', 1)
                ->where('type', 'store')
                ->orderBy('name')
                ->get();

            $cmsCategories = \App\Models\Category::where('store_id', $store->id)
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
