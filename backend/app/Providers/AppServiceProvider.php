<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
    }
}
