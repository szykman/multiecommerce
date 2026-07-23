<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductMedia;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index()
    {
        $media = Media::where(
            'store_id',
            auth()->user()->store_id
        )
        ->latest()
        ->paginate(30);

        return view(
            'admin.media.index',
            compact('media')
        );
    }




    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:51200'
        ]);

        foreach ($request->file('files', []) as $file) {

            $this->mediaService->store(
                $file,
                auth()->user()->store_id,
                'Geral'
            );

        }

        return response()->json([
            'success' => true
        ]);

    }

 public function upload(Request $request)
{
    $request->validate([
        'files.*' => 'required|file|max:51200'
    ]);

    $uploaded = [];

    foreach ($request->file('files', []) as $file) {

        $media = $this->mediaService->store(
            $file,
            auth()->user()->store_id,
            'Produtos'   // <- corrigido
        );

        $uploaded[] = [
            'id'   => $media->id,
            'file' => $media->file
        ];
    }

    return response()->json([
        'success' => true,
        'media'   => $uploaded
    ]);
}

    public function destroy($medium)
    {
        $media = Media::findOrFail($medium);

        if (!auth()->check()) {
            abort(403, 'Usuário não autenticado');
        }

        abort_if(
            $media->store_id != auth()->user()->store_id,
            403
        );

        // produtos onde a mídia é a imagem principal
        $produtosComoPrincipal = Product::where('media_id', $media->id)
            ->pluck('name');

        // produtos onde a mídia está na galeria
        $produtosNaGaleria = Product::whereHas('gallery', function ($q) use ($media) {
                $q->where('media_id', $media->id);
            })
            ->pluck('name');

        // junta os dois, remove duplicados (caso o mesmo produto use a
        // mídia como principal E na galeria) e reindexa
        $produtos = $produtosComoPrincipal
            ->merge($produtosNaGaleria)
            ->unique()
            ->values();

        if ($produtos->isNotEmpty()) {

            $lista = $produtos->implode(', ');

            return back()->with(
                'error',
                'Esta imagem está sendo usada nos produtos: ' . $lista . '. Remova-a desses produtos antes de excluir.'
            );
        }

        Storage::disk('public')->delete($media->file);
        $media->delete();

        return redirect()->route('media.index')->with('success', 'Arquivo excluído.');
    }

}
