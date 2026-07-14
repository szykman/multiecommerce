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

$exists = Media::where(
    'store_id',
    auth()->user()->store_id
)
->where('name', pathinfo(
    $file->getClientOriginalName(),
    PATHINFO_FILENAME
))
->where('size', $file->getSize())
->exists();


if($exists){

    continue;

}

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

return response()->json([
    'success'=>true
]);

    }


public function upload(Request $request)
{

    $request->validate([

        'files.*'=>'required|file|max:51200'

    ]);


    foreach($request->file('files') as $file){


        $path=$file->store(
            'media',
            'public'
        );


        Media::create([

            'store_id'=>auth()->user()->store_id,

            'name'=>$file->getClientOriginalName(),

            'file'=>$path,

            'type'=>explode(
                '/',
                $file->getMimeType()
            )[0],

            'mime'=>$file->getMimeType(),

            'extension'=>$file->extension(),

            'size'=>$file->getSize(),

            'folder'=>'Geral'

        ]);

    }


    return response()->json([

        'success'=>true

    ]);

}


public function destroy(Media $media) { abort_if( $media->store_id != auth()->user()->store_id, 403 ); Storage::disk('public') ->delete($media->file); $media->delete(); return redirect() ->route('media.index') ->with( 'success', 'Arquivo excluído.' ); }


}
