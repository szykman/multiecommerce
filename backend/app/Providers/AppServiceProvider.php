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
 Paginator::useBootstrapFive();
        // A lógica de composer das views do storefront (store/settings/
        // categorias/cmsCategories/cartCount) foi movida para
        // App\Providers\StoreViewServiceProvider, para manter este
        // provider genérico e a lógica da loja centralizada num
        // único lugar dedicado.
    }
}
