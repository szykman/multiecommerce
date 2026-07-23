<?php

namespace App\Http\Controllers\Admin;

use App\Services\MediaService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

   protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index(Request $request)
    {
        $categories = Category::where(
            'store_id',
            auth()->user()->store_id
        )
        ->orderBy('name')
        ->get();

        $products = Product::where(
                'store_id',
                auth()->user()->store_id
            )
            ->whereHas('category', function ($q) {

                $q->where('type', 'store');

            })
            ->with('category')

            ->when($request->search, function ($query) use ($request) {

                $query->where(
                    'name',
                    'like',
                    '%' . $request->search . '%'
                );

            })

            ->when($request->category_id, function ($query) use ($request) {

                $query->where(
                    'category_id',
                    $request->category_id
                );

            })

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
        $categories = Category::where(
            'store_id',
            auth()->user()->store_id
        )
            ->where('active', 1)
            ->where('type', 'store')
            ->orderBy('name')
            ->get();

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )
            ->latest()
            ->get();

        return view(
            'admin.products.create',
            compact(
                'categories',
                'media'
            )
        );
    }

    public function edit(Product $product)
    {
        abort_if(
            $product->store_id != auth()->user()->store_id,
            403
        );

$product->load('gallery.media');


        $categories = Category::where(
            'store_id',
            auth()->user()->store_id
        )
            ->where('active', 1)
            ->where('type', 'store')
            ->orderBy('name')
            ->get();

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )
            ->latest()
            ->get();

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories',
                'media'
            )
        );
    }

public function update(Request $request, Product $product)
{

abort_if(
        $product->store_id != auth()->user()->store_id,
        403
    );

    $data = $request->validate([

        'category_id'      => 'nullable|exists:categories,id',
        'media_id'         => 'nullable|exists:media,id',

        'name'             => 'required|max:255',
        'description'      => 'nullable|string',

        'price'            => 'required|numeric',
        'sale_price'       => 'nullable|numeric|min:0',

        'promotion_start'  => 'nullable|date',
        'promotion_end'    => 'nullable|date|after_or_equal:promotion_start',

        'stock'            => 'required|integer',

        'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

'gallery' => 'nullable|array',

'gallery.*' => 'exists:media,id',



    ]);

    /*
    |----------------------------------------------------------
    | Biblioteca
    |----------------------------------------------------------
    */

    if ($request->filled('media_id')) {

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )
        ->find($request->media_id);

        if ($media) {

            $data['media_id'] = $media->id;
            $data['image'] = $media->file;

        }

    }

    /*
    |----------------------------------------------------------
    | Upload
    |----------------------------------------------------------
    */

    elseif ($request->hasFile('image')) {

$media = $this->mediaService->store(

    $request->file('image'),

    auth()->user()->store_id,

    'Produtos'

);
        $data['media_id'] = $media->id;
        $data['image'] = $media->file;

    }

    $data['slug'] = Str::slug(
        $data['name']
    );

$product->update($data);


/*
|--------------------------------------------------------------------------
| Galeria de imagens
|--------------------------------------------------------------------------
*/

if ($request->has('gallery')) {


    // limpa a galeria atual do produto
    ProductMedia::where(
        'product_id',
        $product->id
    )->delete();



    foreach ($request->gallery as $index => $mediaId) {


        ProductMedia::create([

            'store_id' => auth()->user()->store_id,

            'product_id' => $product->id,

            'media_id' => $mediaId,

            'position' => $index,

            'is_primary' => false,

        ]);


    }

}



return redirect()


        ->route('products.index')
        ->with(
            'success',
            'Produto atualizado com sucesso.'
        );
}

public function store(Request $request)
{
    $data = $request->validate([

        'category_id'      => 'nullable|exists:categories,id',
        'media_id'         => 'nullable|exists:media,id',

        'name'             => 'required|max:255',
        'description'      => 'nullable|string',

        'price'            => 'required|numeric',
        'sale_price'       => 'nullable|numeric|min:0',

        'promotion_start'  => 'nullable|date',
        'promotion_end'    => 'nullable|date|after_or_equal:promotion_start',

        'stock'            => 'required|integer',

        'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

    ]);

    $image = null;

    $mediaId = null;

    /*
    |----------------------------------------------------------
    | Biblioteca
    |----------------------------------------------------------
    */

    if ($request->filled('media_id')) {

        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )
        ->find($request->media_id);

        if ($media) {

            $mediaId = $media->id;
            $image = $media->file;

        }

    }

    /*
    |----------------------------------------------------------
    | Upload
    |----------------------------------------------------------
    */

    elseif ($request->hasFile('image')) {

$media = $this->mediaService->store(

    $request->file('image'),

    auth()->user()->store_id,

    'Produtos'

);
        $mediaId = $media->id;
        $image = $media->file;

    }

    Product::create([

        'store_id'         => auth()->user()->store_id,

        'category_id'      => $data['category_id'],

        'media_id'         => $mediaId,

        'name'             => $data['name'],

        'slug'             => Str::slug($data['name']),

        'description'      => $data['description'] ?? null,

        'image'            => $image,

        'price'            => $data['price'],

        'sale_price'       => $data['sale_price'] ?? null,

        'promotion_start'  => $data['promotion_start'] ?? null,

        'promotion_end'    => $data['promotion_end'] ?? null,

        'stock'            => $data['stock'],

        'active'           => true,

    ]);

    return redirect()
        ->route('products.index')
        ->with(
            'success',
            'Produto cadastrado com sucesso.'
        );
}

public function destroy(Product $product)
{
    abort_if(
        $product->store_id != auth()->user()->store_id,
        403
    );

    /*
     |----------------------------------------------------------
     | Só apaga o arquivo físico se ele NÃO vier da biblioteca.
     | Se existir media_id, o arquivo pertence à biblioteca.
     |----------------------------------------------------------
     */
    if (
        !$product->media_id &&
        $product->image &&
        \Storage::disk('public')->exists($product->image)
    ) {
        \Storage::disk('public')->delete($product->image);
    }

    $product->delete();

    return redirect()
        ->route('products.index')
        ->with(
            'success',
            'Produto excluído com sucesso.'
        );
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
