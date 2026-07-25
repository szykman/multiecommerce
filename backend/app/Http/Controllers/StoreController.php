<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

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


        $product = Product::with([
                'gallery.media',
                'options.values',
                'variants' => function ($q) {
                    $q->where('active', true)
                      ->with('optionValues');
                },
            ])
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


        $rawCart = session()->get('cart', []);

        // Enriquece cada linha do carrinho com dados atuais do banco
        // (miniatura, estoque disponivel agora, se o produto ainda
        // existe/esta ativo) - o preco salvo na sessao e preservado
        // como o preco no momento em que foi adicionado.
        $cartItems = collect($rawCart)->map(function ($item, $key) {

            $product = Product::where('id', $item['id'])
                ->where('active', true)
                ->first();

            $variant = null;

            if (! empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
            }

            $currentStock = $variant
                ? $variant->stock
                : ($product->stock ?? 0);

            return [
                'key' => $key,
                'id' => $item['id'],
                'variant_id' => $item['variant_id'] ?? null,
                'name' => $item['name'],
                'price' => (float) $item['price'],
                'qty' => (int) $item['qty'],
                'subtotal' => (float) $item['price'] * (int) $item['qty'],
                'image' => $product?->image_thumbnail_url,
                'slug' => $product?->slug,
                'current_stock' => $currentStock,
                'available' => (bool) $product && ($variant ? $variant->active : true),
                'exceeds_stock' => (int) $item['qty'] > $currentStock,
            ];

        })->values();

        $cartTotal = $cartItems->sum('subtotal');


        return view(
            'store.cart',
            compact(
                'store',
                'settings',
                'categories',
                'cmsCategories',
                'cartItems',
                'cartTotal'
            )
        );
    }


    /**
     * Atualiza a quantidade de um item do carrinho. Se a quantidade
     * pedida exceder o estoque atual, e limitada automaticamente ao
     * maximo disponivel.
     */
    public function updateCart(Request $request, $key)
    {
        $cart = session()->get('cart', []);

        if (! isset($cart[$key])) {
            return back()->with('error', 'Item nao encontrado no carrinho.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));

        $availableStock = null;

        if (! empty($cart[$key]['variant_id'])) {

            $variant = ProductVariant::find($cart[$key]['variant_id']);
            $availableStock = $variant?->stock;

        } else {

            $product = Product::find($cart[$key]['id']);
            $availableStock = $product?->stock;

        }

        if ($availableStock !== null && $quantity > $availableStock) {
            $quantity = max(1, $availableStock);
        }

        $cart[$key]['qty'] = $quantity;

        session()->put('cart', $cart);

        return back()->with('success', 'Carrinho atualizado.');
    }


    /**
     * Remove um item especifico do carrinho, pela chave composta
     * (produto ou produto-variante), nao pelo ID do produto sozinho
     * - necessario porque duas variantes do mesmo produto podem
     * coexistir como linhas separadas.
     */
    public function removeFromCart($key)
    {
        $cart = session()->get('cart', []);

        unset($cart[$key]);

        session()->put('cart', $cart);

        return back()->with('success', 'Item removido do carrinho.');
    }


    /**
     * Esvazia o carrinho inteiro.
     */
    public function clearCart()
    {
        session()->forget('cart');

        return back()->with('success', 'Carrinho esvaziado.');
    }




    public function addToCart(Request $request, $slug)
    {
        $tenant = app(\App\Services\TenantManager::class);

        $store = $tenant->getStore();


        $product = Product::where(
            'store_id',
            $store->id
        )
        ->where('slug',$slug)
        ->firstOrFail();


        $quantity = max(1, (int) $request->input('quantity', 1));

        $variant = null;

        if ($request->filled('variant_id')) {

            $variant = ProductVariant::where('id', $request->input('variant_id'))
                ->where('product_id', $product->id)
                ->where('active', true)
                ->first();

            if (! $variant) {
                return back()->with(
                    'error',
                    'A variação selecionada não está disponível.'
                );
            }
        }

        // Se o produto tem variação, exige que uma tenha sido escolhida
        if ($product->has_variants && ! $variant) {
            return back()->with(
                'error',
                'Selecione as opções do produto antes de adicionar ao carrinho.'
            );
        }

        $availableStock = $variant ? $variant->stock : $product->stock;

        if ($availableStock < 1) {
            return back()->with(
                'error',
                'Produto sem estoque disponível.'
            );
        }

        $price = $variant
            ? ($variant->sale_price ?: $variant->price)
            : $product->current_price;

        // Chave composta (produto + variante) para que combinações
        // diferentes do mesmo produto virem linhas separadas no carrinho.
        $cartKey = $variant
            ? $product->id.'-'.$variant->id
            : (string) $product->id;

        $cart = session()->get('cart', []);

        $newQty = ($cart[$cartKey]['qty'] ?? 0) + $quantity;

        if ($newQty > $availableStock) {
            $newQty = $availableStock;
        }

        $cart[$cartKey] = [

            'id' => $product->id,
            'variant_id' => $variant?->id,
            'name' => $product->name.($variant ? ' — '.$variant->optionValues->pluck('value')->implode(' / ') : ''),
            'price' => $price,
            'image' => $product->image,
            'qty' => $newQty,

        ];


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
