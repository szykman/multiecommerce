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

        $view->with([
            'store'=>$store,
            'cartCount'=>$cartCount
        ]);

    });

    }
}
