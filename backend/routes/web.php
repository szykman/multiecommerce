<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StoreController;

use App\Services\TenantManager;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\Admin\PageController;


/*
|--------------------------------------------------------------------------
| TESTE TENANT
|--------------------------------------------------------------------------
*/

Route::get('/tenant-test', function (TenantManager $tenant) {

    return [
        'store_id' => $tenant->id(),
        'store' => $tenant->getStore()?->name,
    ];

})->middleware('tenant');



/*
|--------------------------------------------------------------------------
| LOJA PÚBLICA
|--------------------------------------------------------------------------
*/

Route::middleware('tenant')->group(function () {


    Route::get(
        '/',
        [StoreController::class,'index']
    );


    Route::get(
        '/produto/{slug}',
        [StoreController::class,'product']
    )
    ->name('store.product');


    Route::get(
        '/categoria/{slug}',
        [StoreController::class,'category']
    )
    ->name('store.category');


    Route::get(
        '/pagina/{slug}',
        [StoreController::class,'page']
    )
    ->name('store.page');


    Route::get(
        '/carrinho',
        [StoreController::class,'cart']
    )
    ->name('store.cart');


    Route::post(
        '/carrinho/adicionar/{slug}',
        [StoreController::class,'addToCart']
    )
    ->name('store.cart.add');


    Route::post(
        '/carrinho/remover/{id}',
        [StoreController::class,'removeFromCart']
    )
    ->name('store.cart.remove');


    Route::post(
        '/favorites/toggle/{product}',
        [FavoriteController::class,'toggle']
    )
    ->name('favorites.toggle');


});





/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/


Route::get(
    '/admin/login',
    [AuthController::class,'loginForm']
)
->name('admin.login');


Route::post(
    '/admin/login',
    [AuthController::class,'login']
);


Route::post(
    '/admin/logout',
    [AuthController::class,'logout']
)
->name('admin.logout');





/*
|--------------------------------------------------------------------------
| ÁREA ADMINISTRATIVA
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'admin'
])
->prefix('admin')
->group(function(){



    Route::get(
        '/',
        [DashboardController::class,'index']
    )
    ->name('admin.dashboard');



    /*
    |--------------------------------------------------------------------------
    | Categorias
    |--------------------------------------------------------------------------
    */


    Route::resource(
        'categories',
        CategoryController::class
    );


    Route::patch(
        'categories/{category}/toggle',
        [CategoryController::class,'toggle']
    )
    ->name('categories.toggle');



    /*
    |--------------------------------------------------------------------------
    | Produtos
    |--------------------------------------------------------------------------
    */


    Route::resource(
        'products',
        ProductController::class
    );


    Route::patch(
        'products/{product}/toggle',
        [ProductController::class,'toggle']
    )
    ->name('products.toggle');



    /*
    |--------------------------------------------------------------------------
    | Páginas CMS
    |--------------------------------------------------------------------------
    */


    Route::resource(
        'pages',
        PageController::class
    );



    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */


    Route::get(
        'settings',
        [StoreSettingsController::class,'edit']
    )
    ->name('settings.edit');


    Route::put(
        'settings',
        [StoreSettingsController::class,'update']
    )
    ->name('settings.update');



    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */


    Route::resource(
        'media',
        MediaController::class
    );


Route::post(
    'media/upload',
    [MediaController::class,'upload']   // <- corrigido
)
->name('media.upload');

});
