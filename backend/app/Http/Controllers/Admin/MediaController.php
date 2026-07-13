<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
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

            $path = $file->store('media', 'public');

            $type = explode('/', $file->getMimeType())[0];

            Media::create([

                'store_id'  => auth()->user()->store_id,

                'name'      => pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                ),

                'file'      => $path,

                'type'      => $type,

                'mime'      => $file->getMimeType(),

                'extension' => $file->extension(),

                'size'      => $file->getSize(),

                'folder'    => 'Geral'

            ]);
        }

        return redirect()
            ->route('media.index')
            ->with('success', 'Arquivos enviados.');
    }
}
