<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\Media;

class StoreSettingsController extends Controller
{
    public function edit()
    {
        $store = Store::findOrFail(
            auth()->user()->store_id
        );

        $settings = StoreSetting::firstOrCreate([
            'store_id' => $store->id
        ]);


$media = Media::where(
    'store_id',
    $store->id
)
->latest()
->get();

        return view(
            'admin.settings.edit',
            compact(
                'store',
                'settings',
  'media'
            )
        );
    }



public function update(Request $request)
{
    $store = Store::findOrFail(
        auth()->user()->store_id
    );


    $settings = StoreSetting::firstOrCreate([
        'store_id'=>$store->id
    ]);


    $data = $request->except([
        '_token',
        '_method',
        'logo',
        'banner',
        'favicon',
        'name'
    ]);



if($request->hasFile('logo')){

    $media = $this->storeMedia(
        $request->file('logo')
    );

    $data['logo'] = $media->file;

}


 
if($request->hasFile('banner')){

    $media = $this->storeMedia(
        $request->file('banner')
    );

    $data['banner'] = $media->file;

}



if($request->hasFile('favicon')){

    $media = $this->storeMedia(
        $request->file('favicon')
    );

    $data['favicon'] = $media->file;

}


// Biblioteca de mídia

if($request->logo_media_id){

    $media = Media::where('id',$request->logo_media_id)
        ->where('store_id',$store->id)
        ->first();

    if($media){

        $data['logo'] = $media->file;

    }

}


if($request->banner_media_id){

    $media = Media::where('id',$request->banner_media_id)
        ->where('store_id',$store->id)
        ->first();

    if($media){

        $data['banner'] = $media->file;

    }

}


if($request->favicon_media_id){

    $media = Media::where('id',$request->favicon_media_id)
        ->where('store_id',$store->id)
        ->first();

    if($media){

        $data['favicon'] = $media->file;

    }

}


    // grava campos normais da tabela

    $settings->update($data);



    // mantém JSON para futuro

    if($request->has('settings')){

        $settings->settings =
            array_merge(
                $settings->settings ?? [],
                $request->settings
            );

        $settings->save();

    }



    // nome pertence a tabela stores

    $store->name =
        $request->name;

    $store->save();



    return redirect()
        ->route('settings.edit')
        ->with(
            'success',
            'Configurações salvas.'
        );
}



private function storeMedia($file)
{
    $path = $file->store('media', 'public');

    return Media::create([

        'store_id'  => auth()->user()->store_id,

        'name'      => pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        ),

        'file'      => $path,

        'type'      => explode(
            '/',
            $file->getMimeType()
        )[0],

        'mime'      => $file->getMimeType(),

        'extension' => $file->extension(),

        'size'      => $file->getSize(),

        'folder'    => 'Geral',

    ]);
}



}
