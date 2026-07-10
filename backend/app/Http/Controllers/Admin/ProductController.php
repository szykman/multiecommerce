<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{

public function index(Request $request)
{
    $categories = \App\Models\Category::where(
        'store_id',
        auth()->user()->store_id
    )
    ->orderBy('name')
    ->get();

    $products = Product::where(
            'store_id',
            auth()->user()->store_id
        )
        ->with('category')

        ->when($request->search, function ($query) use ($request) {

            $query->where(
                'name',
                'like',
                '%'.$request->search.'%'
            );

        })

        ->when($request->category_id, function ($query) use ($request) {

            $query->where(
                'category_id',
                $request->category_id
            );

        })

     //  ->latest()
       ->orderBy(

    $request->get('sort', 'id'),

    $request->get('direction', 'desc')

)
	->paginate(10)
        ->withQueryString();

    return view(
        'admin.products.index',
        compact(
            'products',
            'categories'
        )
    );
}



  
public function create()
{
    $categories = \App\Models\Category::where(
        'store_id',
        auth()->user()->store_id
    )
    ->orderBy('name')
    ->get();

    return view(
        'admin.products.create',
        compact('categories')
    );
}



public function edit(Product $product)
{
    abort_if(
        $product->store_id != auth()->user()->store_id,
        403
    );

   $categories = \App\Models\Category::where(
       'store_id',
       auth()->user()->store_id
   )->orderBy('name')->get();

    return view(
       'admin.products.edit',
      compact('product', 'categories')
  );
}

public function update(Request $request, Product $product)
{
    abort_if(
        $product->store_id != auth()->user()->store_id,
        403
    );

$data = $request->validate([
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'price' => 'required|numeric',
    'stock' => 'required|integer',
    'category_id' => 'nullable|exists:categories,id',
    'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
]);

    if ($request->hasFile('image')) {

        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $data['image'] = $request->file('image')
            ->store('products', 'public');
    }

    $data['slug'] = \Str::slug($data['name']);

    $product->update($data);

    return redirect()
        ->route('products.index')
        ->with('success', 'Produto atualizado com sucesso.');
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $image = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('products', 'public');
        }

Product::create([
    'store_id' => auth()->user()->store_id,
    'category_id' => $data['category_id'] ?? null,
    'name' => $data['name'],
    'slug' => Str::slug($data['name']),
    'description' => $data['description'] ?? null,
    'price' => $data['price'],
    'stock' => $data['stock'],
    'image' => $image,
    'active' => true,
]);
        return redirect('/admin/products')
            ->with('success', 'Produto cadastrado com sucesso.');
    }


public function destroy(Product $product)
{
    abort_if(
        $product->store_id != auth()->user()->store_id,
        403
    );

    if (
        $product->image &&
        \Storage::disk('public')->exists($product->image)
    ) {
        \Storage::disk('public')->delete($product->image);
    }

    $product->delete();

    return redirect()
        ->route('products.index')
        ->with('success', 'Produto excluído com sucesso.');
}

public function toggle(Product $product)
{
    abort_if(
        $product->store_id != auth()->user()->store_id,
        403
    );

    $product->update([
        'active' => !$product->active
    ]);

    return back()->with(
        'success',
        'Status atualizado.'
    );
}

}
