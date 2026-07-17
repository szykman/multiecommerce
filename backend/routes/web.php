<?php
use App\Http\Controllers\Admin\CategoryController;
use App\Services\TenantManager;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MediaController;

Route::get('/tenant-test', function (TenantManager $tenant) {
    return [
        'store_id' => $tenant->id(),
        'store' => $tenant->getStore()?->name,
    ];
})->middleware('tenant');

//Route::get('/', [StoreController::class,'index']);

Route::middleware('tenant')->group(function () {

    Route::get('/', [StoreController::class,'index']);

Route::get(
    '/produto/{slug}',
    [StoreController::class,'product']
)->name('store.product');

Route::get(
    '/carrinho',
    [StoreController::class,'cart']
)->name('store.cart');

Route::post(
    '/carrinho/adicionar/{slug}',
    [StoreController::class,'addToCart']
)->name('store.cart.add');

Route::post(
    '/carrinho/remover/{id}',
    [StoreController::class,'removeFromCart']
)->name('store.cart.remove');

Route::get(
    '/pagina/{slug}',
    [StoreController::class,'page']
)->name('store.page');

    Route::get(
        '/categoria/{slug}',
        [StoreController::class,'category']
    )->name('store.category');


// Route::get(
//    '/categoria/{slug}',
//    [StoreController::class,'category']
// )->name('store.category');

Route::get('/admin/login', [AuthController::class, 'loginForm'])
    ->name('admin.login');

Route::get('/admin', [DashboardController::class, 'index'])
    ->middleware(['auth','admin']);

Route::resource('/admin/categories', CategoryController::class)
    ->middleware(['auth','admin']);

Route::patch(
    '/admin/categories/{category}/toggle',
    [CategoryController::class, 'toggle']
)
->name('categories.toggle')
->middleware(['auth','admin']);


Route::resource('/admin/products', ProductController::class)
    ->middleware(['auth','admin']);

Route::patch(
    '/admin/products/{product}/toggle',
    [ProductController::class, 'toggle']
)->name('products.toggle')
 ->middleware(['auth','admin']);

Route::post('/admin/login', [AuthController::class, 'login']);

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->name('admin.logout');


Route::resource(
    'admin/pages',
    App\Http\Controllers\Admin\PageController::class
)->middleware(['auth','admin']);


Route::get(
    '/admin/settings',
    [App\Http\Controllers\Admin\StoreSettingsController::class,'edit']
)->name('settings.edit')
->middleware(['auth','admin']);

Route::put(
    '/admin/settings',
    [App\Http\Controllers\Admin\StoreSettingsController::class,'update']
)->name('settings.update')
->middleware(['auth','admin']);

Route::middleware(['auth'])
    ->prefix('admin')
    ->group(function () {

        Route::resource(
            'media',
            \App\Http\Controllers\Admin\MediaController::class
        )->parameters([
            'media' => 'media',
        ]);

});

Route::post(
    '/admin/media/upload',
    [MediaController::class,'store']
)
->name('media.upload');


    // Futuras rotas:
    // Route::get('/produto/{slug}', ...);
    // Route::get('/carrinho', ...);

});
