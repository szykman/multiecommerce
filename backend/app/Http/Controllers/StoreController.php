<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StoreSetting;

class StoreController extends Controller
{

    public function index()
    {
        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();

        $settings = StoreSetting::firstOrCreate([
            'store_id' => $store->id
        ]);


        $categories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','store')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->withCount('products')
        ->orderBy('name')
        ->get();


        $cmsCategories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','cms')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->orderBy('name')
        ->get();


        $products = Product::where(
            'store_id',
            $store->id
        )
        ->whereHas('category',function($q){

            $q->where('type','store');

        })
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

$favorites = session()->get('favorites', []);

        return view(
            'store.home',
            compact(
                'store',
                'settings',
                'categories',
                'cmsCategories',
                'products',
		'favorites'
            )
        );
    }





    public function category($slug)
    {
        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();

        $settings = StoreSetting::firstOrCreate([
            'store_id' => $store->id
        ]);


        $category = Category::where(
            'store_id',
            $store->id
        )
        ->where('slug',$slug)
        ->where('active',1)
        ->firstOrFail();


        $categories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','store')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->withCount('products')
        ->orderBy('name')
        ->get();


        $cmsCategories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','cms')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->orderBy('name')
        ->get();


        $products = Product::where(
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

$favorites = session()->get('favorites', []);

        return view(
            'store.category',
            compact(
                'store',
                'settings',
                'category',
                'categories',
                'cmsCategories',
                'products',
		'favorites'
            )
        );

    }





    public function product($slug)
    {
        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();

        $settings = StoreSetting::firstOrCreate([
            'store_id' => $store->id
        ]);


        $product = Product::with('gallery.media')
    ->where(
        'store_id',
        $store->id
    )
    ->where('slug',$slug)
    ->where('active',1)
    ->firstOrFail();


        $categories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','store')
        ->orderBy('name')
        ->get();


        $cmsCategories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','cms')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->orderBy('name')
        ->get();


        $relatedProducts = Product::where(
            'store_id',
            $store->id
        )
        ->where('category_id',$product->category_id)
        ->where('id','!=',$product->id)
        ->where('active',1)
        ->take(4)
        ->get();

$favorites = session()->get('favorites', []);

        return view(
            'store.product',
            compact(
                'store',
                'settings',
                'product',
                'categories',
                'cmsCategories',
                'relatedProducts',
		'favorites'
            )
        );
    }

    public function cart()
    {
        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();

        $settings = StoreSetting::firstOrCreate([
            'store_id' => $store->id
        ]);


        $categories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','store')
        ->orderBy('name')
        ->get();


        $cmsCategories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','cms')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->orderBy('name')
        ->get();


        $cart = session()->get('cart', []);


        return view(
            'store.cart',
            compact(
                'store',
                'settings',
                'categories',
                'cmsCategories',
                'cart'
            )
        );
    }






    public function addToCart($slug)
    {
        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();


        $product = Product::where(
            'store_id',
            $store->id
        )
        ->where('slug',$slug)
        ->firstOrFail();


        $cart = session()->get('cart', []);


        if(isset($cart[$product->id])){

            $cart[$product->id]['qty']++;

        }else{

            $cart[$product->id] = [

                'id'=>$product->id,
                'name'=>$product->name,
                'price'=>$product->price,
                'image'=>$product->image,
                'qty'=>1

            ];

        }


        session()->put('cart',$cart);


        return redirect()
            ->route('store.cart')
            ->with('success','Produto adicionado ao carrinho.');
    }






    public function page($slug)
    {

        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();

        $settings = StoreSetting::firstOrCreate([
            'store_id' => $store->id
        ]);


        $page = Product::where(
            'store_id',
            $store->id
        )
        ->where('slug',$slug)
        ->where('active',1)
        ->whereHas('category',function($q){

            $q->where('type','cms');

        })
        ->firstOrFail();


        $categories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','store')
        ->orderBy('name')
        ->get();


        $cmsCategories = Category::where(
            'store_id',
            $store->id
        )
        ->where('active',1)
        ->where('type','cms')
        ->with(['products'=>function($q){

            $q->where('active',1)
              ->orderBy('name');

        }])
        ->orderBy('name')
        ->get();


        return view(
            'store.page',
            compact(
                'store',
                'settings',
                'page',
                'categories',
                'cmsCategories'
            )
        );
    }

}
