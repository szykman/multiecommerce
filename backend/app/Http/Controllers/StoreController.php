<?php

namespace App\Http\Controllers;

use App\Models\Product;

class StoreController extends Controller
{


public function index()
{
    $tenant = app(\App\Services\TenantManager::class);

    $store = $tenant->getStore();


    $categories = \App\Models\Category::where(
        'store_id',
        $store->id
    )
    ->where('active',1)
    ->withCount('products')
    ->orderBy('name')
    ->get();



    $products = \App\Models\Product::where(
        'store_id',
        $store->id
    )
    ->where('active',1)

    ->when(request('search'), function ($query) {

        $query->where(
            'name',
            'like',
            '%'.request('search').'%'
        );

    })

    ->latest()
    ->take(8)
    ->get();



    return view(
        'store.home',
        compact(
            'store',
            'categories',
            'products'
        )
    );
}

public function category($slug)
{
    $tenant = app(\App\Services\TenantManager::class);

    $store = $tenant->getStore();

    $category = \App\Models\Category::where(
        'store_id',
        $store->id
    )
    ->where('slug',$slug)
    ->where('active',1)
    ->firstOrFail();

    $categories = \App\Models\Category::where(
        'store_id',
        $store->id
    )
    ->where('active',1)
    ->withCount('products')
    ->orderBy('name')
    ->get();

$products = \App\Models\Product::where(
    'store_id',
    $store->id
)
->where('category_id',$category->id)
->where('active',1)

->when(request('search'), function ($query) {

    $query->where(
        'name',
        'like',
        '%'.request('search').'%'
    );

})

->orderBy(
    request('sort','name'),
    request('direction','asc')
)

->paginate(12)
->withQueryString();

return view(
    'store.category',
    compact(
        'store',
        'category',
        'categories',
        'products'
    )
);


}
}
