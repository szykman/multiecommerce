<?php
use App\Services\TenantManager;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;


Route::get('/tenant-test', function (TenantManager $tenant) {
    return [
        'store_id' => $tenant->id(),
        'store' => $tenant->getStore()?->name,
    ];
})->middleware('tenant');

Route::get('/', [StoreController::class,'index']);

Route::get('/admin/login', [AuthController::class, 'loginForm'])
    ->name('admin.login');

Route::get('/admin', [DashboardController::class, 'index'])
    ->middleware(['auth','admin']);

Route::resource('/admin/products', ProductController::class)
    ->middleware(['auth','admin']);

Route::post('/admin/login', [AuthController::class, 'login']);

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->name('admin.logout');

