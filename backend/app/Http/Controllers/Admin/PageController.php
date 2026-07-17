<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index()
    {
        $categories = Category::where(
            'store_id',
            auth()->user()->store_id
        )
        ->where('type','cms')
        ->where('active',1)
        ->with([
            'products' => function($q){

                $q->orderBy('name');

            }
        ])
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

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )
        ->latest()
        ->get();

        return view(
            'admin.pages.create',
            compact(
                'categories',
                'media'
            )
        );
    }

public function store(Request $request)
{
    $data = $request->validate([

        'category_id' => 'required|exists:categories,id',

        'media_id' => 'nullable|exists:media,id',

        'name' => 'required|max:255',

        'description' => 'nullable',

        'image' => 'nullable|image|max:4096',

        'active' => 'nullable'

    ]);

    $image = null;

    $mediaId = null;

    /*
    |--------------------------------------------------------------------------
    | Biblioteca
    |--------------------------------------------------------------------------
    */

    if ($request->filled('media_id')) {

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )->find($request->media_id);

        if ($media) {

            $mediaId = $media->id;

            $image = $media->file;

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    elseif ($request->hasFile('image')) {

        $media = $this->mediaService->store(

            $request->file('image'),

            auth()->user()->store_id,

            'CMS'

        );

        $mediaId = $media->id;

        $image = $media->file;

    }

    Product::create([

        'store_id' => auth()->user()->store_id,

        'category_id' => $data['category_id'],

        'media_id' => $mediaId,

        'name' => $data['name'],

        'slug' => Str::slug($data['name']),

        'description' => $data['description'],

        'image' => $image,

        'price' => 0,

        'stock' => 0,

        'active' => $request->boolean('active')

    ]);

    return redirect()
        ->route('pages.index')
        ->with(
            'success',
            'Página criada com sucesso.'
        );
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

    $media = Media::where(
        'store_id',
        auth()->user()->store_id
    )
    ->latest()
    ->get();

    return view(
        'admin.pages.edit',
        compact(
            'page',
            'categories',
            'media'
        )
    );
}

public function update(Request $request, Product $page)
{
    abort_if(
        $page->store_id != auth()->user()->store_id,
        403
    );

    $data = $request->validate([

        'category_id' => 'required|exists:categories,id',

        'media_id' => 'nullable|exists:media,id',

        'name' => 'required|max:255',

        'description' => 'nullable',

        'image' => 'nullable|image|max:4096',

        'active' => 'nullable'

    ]);

    /*
    |--------------------------------------------------------------------------
    | Biblioteca
    |--------------------------------------------------------------------------
    */

    if ($request->filled('media_id')) {

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )->find($request->media_id);

        if ($media) {

            $data['media_id'] = $media->id;

            $data['image'] = $media->file;

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    elseif ($request->hasFile('image')) {

        $media = $this->mediaService->store(

            $request->file('image'),

            auth()->user()->store_id,

            'CMS'

        );

        $data['media_id'] = $media->id;

        $data['image'] = $media->file;

    }

    $page->update([

        'category_id' => $data['category_id'],

        'media_id' => $data['media_id'] ?? $page->media_id,

        'name' => $data['name'],

        'slug' => Str::slug($data['name']),

        'description' => $data['description'],

        'image' => $data['image'] ?? $page->image,

        'active' => $request->boolean('active')

    ]);

    return redirect()
        ->route('pages.index')
        ->with(
            'success',
            'Página atualizada.'
        );
}

public function destroy(Product $page)
{
    abort_if(
        $page->store_id != auth()->user()->store_id,
        403
    );

    /*
    |--------------------------------------------------------------------------
    | Só remove arquivo físico se NÃO pertence à biblioteca
    |--------------------------------------------------------------------------
    */

    if (

        !$page->media_id &&

        $page->image &&

        \Storage::disk('public')->exists($page->image)

    ) {

        \Storage::disk('public')->delete($page->image);

    }

    $page->delete();

    return redirect()
        ->route('pages.index')
        ->with(
            'success',
            'Página removida.'
        );
}

}

