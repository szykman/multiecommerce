<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Store\CustomerAuthController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\AddressController;

use App\Services\TenantManager;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\StoreSettingsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentSettingsController;


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
        '/carrinho/remover/{key}',
        [StoreController::class,'removeFromCart']
    )
    ->name('store.cart.remove');


    Route::post(
        '/carrinho/atualizar/{key}',
        [StoreController::class,'updateCart']
    )
    ->name('store.cart.update');


    Route::post(
        '/carrinho/limpar',
        [StoreController::class,'clearCart']
    )
    ->name('store.cart.clear');


    Route::post(
        '/favorites/toggle/{product}',
        [FavoriteController::class,'toggle']
    )
    ->name('favorites.toggle');


    /*
    |--------------------------------------------------------------------------
    | Autenticação do Cliente (Customer)
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cliente/cadastro',
        [CustomerAuthController::class,'registerForm']
    )
    ->name('store.register');

    Route::post(
        '/cliente/cadastro',
        [CustomerAuthController::class,'register']
    );

    Route::get(
        '/cliente/login',
        [CustomerAuthController::class,'loginForm']
    )
    ->name('store.login');

    Route::post(
        '/cliente/login',
        [CustomerAuthController::class,'login']
    );

    Route::post(
        '/cliente/logout',
        [CustomerAuthController::class,'logout']
    )
    ->name('store.logout');

    Route::get(
        '/cliente/esqueci-senha',
        [CustomerAuthController::class,'forgotPasswordForm']
    )
    ->name('store.password.request');

    Route::post(
        '/cliente/esqueci-senha',
        [CustomerAuthController::class,'sendResetLink']
    )
    ->name('store.password.email');

    Route::get(
        '/cliente/redefinir-senha/{token}/{email}',
        [CustomerAuthController::class,'resetPasswordForm']
    )
    ->name('store.password.reset');

    Route::post(
        '/cliente/redefinir-senha',
        [CustomerAuthController::class,'resetPassword']
    )
    ->name('store.password.update');

    Route::middleware('auth:customer')->group(function () {

        Route::get(
            '/cliente/conta',
            [CustomerAuthController::class,'account']
        )
        ->name('store.account');

        Route::put(
            '/cliente/conta',
            [CustomerAuthController::class,'updateAccount']
        )
        ->name('store.account.update');

        Route::get(
            '/cliente/enderecos',
            [AddressController::class,'index']
        )
        ->name('store.addresses');

        Route::delete(
            '/cliente/enderecos/{address}',
            [AddressController::class,'destroy']
        )
        ->name('store.addresses.destroy');

        Route::get(
            '/checkout/endereco',
            [CheckoutController::class,'address']
        )
        ->name('store.checkout.address');

        Route::post(
            '/checkout/endereco',
            [CheckoutController::class,'storeAddress']
        )
        ->name('store.checkout.address.store');

        Route::get(
            '/checkout/revisao',
            [CheckoutController::class,'review']
        )
        ->name('store.checkout.review');

        Route::post(
            '/checkout/finalizar',
            [CheckoutController::class,'placeOrder']
        )
        ->name('store.checkout.place');

        Route::get(
            '/checkout/confirmacao/{order}',
            [CheckoutController::class,'confirmation']
        )
        ->name('store.checkout.confirmation');

        Route::get(
            '/checkout/pagamento/{order}',
            [CheckoutController::class,'choosePayment']
        )
        ->name('store.checkout.payment');

        Route::post(
            '/checkout/pagamento/{order}',
            [CheckoutController::class,'selectPayment']
        )
        ->name('store.checkout.payment.select');

        Route::get(
            '/checkout/pagar/{order}',
            [CheckoutController::class,'showPayment']
        )
        ->name('store.checkout.pay');

        Route::post(
            '/checkout/pagar/{order}/confirmar',
            [CheckoutController::class,'confirmPaidByCustomer']
        )
        ->name('store.checkout.payment.confirm');

        Route::get(
            '/cliente/pedidos',
            [CheckoutController::class,'myOrders']
        )
        ->name('store.orders');

    });


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
    | Variações de Produto
    |--------------------------------------------------------------------------
    */

    Route::post(
        'products/{product}/variants/generate',
        [ProductVariantController::class, 'generateOptions']
    )
    ->name('products.variants.generate');

    Route::post(
        'products/{product}/variants/update',
        [ProductVariantController::class, 'updateVariants']
    )
    ->name('products.variants.update');

    Route::delete(
        'products/variants/{variant}',
        [ProductVariantController::class, 'destroy']
    )
    ->name('products.variants.destroy');

    Route::post(
        'products/{product}/variants/disable',
        [ProductVariantController::class, 'disable']
    )
    ->name('products.variants.disable');



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
    | Clientes
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'customers',
        AdminCustomerController::class
    )
    ->only(['index', 'show']);


    /*
    |--------------------------------------------------------------------------
    | Pedidos
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'orders',
        AdminOrderController::class
    )
    ->only(['index', 'show']);

    Route::patch(
        'orders/{order}/status',
        [AdminOrderController::class, 'updateStatus']
    )
    ->name('orders.status');

    Route::post(
        'orders/{order}/confirm-payment',
        [AdminOrderController::class, 'confirmPayment']
    )
    ->name('orders.payment.confirm');


    /*
    |--------------------------------------------------------------------------
    | Formas de Pagamento
    |--------------------------------------------------------------------------
    */

    Route::get(
        'payment-settings',
        [PaymentSettingsController::class, 'edit']
    )
    ->name('payment-settings.edit');

    Route::put(
        'payment-settings',
        [PaymentSettingsController::class, 'update']
    )
    ->name('payment-settings.update');



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
