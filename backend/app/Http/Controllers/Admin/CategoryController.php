<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {

$categories = Category::where(
    'store_id',
    auth()->user()->store_id
)
->withCount('products')
->latest()
->get();
        return view(
            'admin.categories.index',
            compact('categories')
        );
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'name' => 'required|max:255',

        ]);

        Category::create([

            'store_id' => auth()->user()->store_id,

            'name' => $data['name'],

            'slug' => Str::slug($data['name']),

            'active' => true,

        ]);

        return redirect()
            ->route('categories.index')
            ->with('success','Categoria criada com sucesso.');
    }

    public function edit(Category $category)
    {
        abort_if(
            $category->store_id != auth()->user()->store_id,
            403
        );

        return view(
            'admin.categories.edit',
            compact('category')
        );
    }

    public function update(
        Request $request,
        Category $category
    )
    {
        abort_if(
            $category->store_id != auth()->user()->store_id,
            403
        );

        $data = $request->validate([

            'name' => 'required|max:255',

            'active' => 'required|boolean',

        ]);

        $category->update([

            'name' => $data['name'],

            'slug' => Str::slug($data['name']),

            'active' => $data['active'],

        ]);

        return redirect()
            ->route('categories.index')
            ->with('success','Categoria atualizada.');
    }

public function destroy(Category $category)
{
    abort_if(
        $category->store_id != auth()->user()->store_id,
        403
    );

    if($category->products()->count() > 0){

        return redirect()
            ->route('categories.index')
            ->with(
                'error',
                'Esta categoria possui produtos vinculados e não pode ser excluída.'
            );

    }


    $category->delete();


    return redirect()
        ->route('categories.index')
        ->with(
            'success',
            'Categoria removida com sucesso.'
        );
}

}
