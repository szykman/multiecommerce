<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $categories = Category::where(
            'store_id',
            auth()->user()->store_id
        )
        ->where('type','cms')
        ->where('active',1)
        ->with(['products' => function($q){

            $q->orderBy('name');

        }])
        ->orderBy('name')
        ->get();

        return view(
            'admin.pages.index',
            compact('categories')
        );
    }

    public function create()
    {

    $categories = Category::where(
        'store_id',
        auth()->user()->store_id
    )
    ->where('type','cms')
    ->where('active',1)
    ->orderBy('name')
    ->get();

    return view(
        'admin.pages.create',
        compact('categories')
    );
}

    public function store(Request $request)
    {

    $data = $request->validate([

        'category_id' => 'required|exists:categories,id',

        'name' => 'required|max:255',

        'description' => 'nullable',

        'image' => 'nullable|image|max:4096',

        'active' => 'nullable'

    ]);

    $image = null;

    if($request->hasFile('image')){

        $image = $request
            ->file('image')
            ->store('products','public');

    }

    Product::create([

        'store_id' => auth()->user()->store_id,

        'category_id' => $data['category_id'],

        'name' => $data['name'],

        'slug' => \Illuminate\Support\Str::slug($data['name']),

        'description' => $data['description'],

        'image' => $image,

        'price' => 0,

        'stock' => 0,

        'weight' => 0,

        'active' => $request->boolean('active')

    ]);

    return redirect()
        ->route('pages.index')
        ->with('success','Página criada com sucesso.');

    }

    public function show(string $id)
    {
        //
    }

    public function edit(Product $page)
    {

    abort_if(
        $page->store_id != auth()->user()->store_id,
        403
    );

    $categories = Category::where(
        'store_id',
        auth()->user()->store_id
    )
    ->where('type','cms')
    ->where('active',1)
    ->orderBy('name')
    ->get();

    return view(
        'admin.pages.edit',
        compact(
            'page',
            'categories'
        )
    );
        //
    }

    public function update(Request $request, Product $page)
    {
 
    abort_if(
        $page->store_id != auth()->user()->store_id,
        403
    );

    $data = $request->validate([

        'category_id'=>'required|exists:categories,id',

        'name'=>'required|max:255',

        'description'=>'nullable',

        'image'=>'nullable|image|max:4096'

    ]);

    if($request->hasFile('image')){

        $data['image']=$request
            ->file('image')
            ->store('products','public');

    }

    $page->update([

        'category_id'=>$data['category_id'],

        'name'=>$data['name'],

        'slug'=>\Illuminate\Support\Str::slug($data['name']),

        'description'=>$data['description'],

        'image'=>$data['image'] ?? $page->image,

        'active'=>$request->boolean('active')

    ]);

    return redirect()
        ->route('pages.index')
        ->with('success','Página atualizada.');
       //
    }

    public function destroy(string $id)
    {
        //
    }
}
