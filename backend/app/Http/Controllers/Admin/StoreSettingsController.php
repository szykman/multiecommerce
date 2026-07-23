<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\Media;
use App\Services\MediaService;

class StoreSettingsController extends Controller
{

    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

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



        /*
        |--------------------------------------------------------------------------
        | Campos normais da tabela store_settings
        |--------------------------------------------------------------------------
        */


        $data = $request->except([

            '_token',
            '_method',

            'logo',
            'banner',
            'favicon',

            'logo_media_id',
            'banner_media_id',
            'favicon_media_id',

            'name',

            // JSON separado
            'settings'

        ]);





        /*
        |--------------------------------------------------------------------------
        | Upload de arquivos
        |--------------------------------------------------------------------------
        */


        if($request->hasFile('logo')){


            $media = $this->mediaService->store(
                $request->file('logo'),
                $store->id,
                'Geral'
            );


            $data['logo'] = $media->file;


        }




        if($request->hasFile('banner')){


            $media = $this->mediaService->store(
                $request->file('banner'),
                $store->id,
                'Geral'
            );


            $data['banner'] = $media->file;


        }





        if($request->hasFile('favicon')){


            $media = $this->mediaService->store(
                $request->file('favicon'),
                $store->id,
                'Geral'
            );


            $data['favicon'] = $media->file;


        }





        /*
        |--------------------------------------------------------------------------
        | Seleção pela biblioteca de mídia
        |--------------------------------------------------------------------------
        */


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





        /*
        |--------------------------------------------------------------------------
        | Salva campos normais
        |--------------------------------------------------------------------------
        */


        $settings->update($data);







        /*
        |--------------------------------------------------------------------------
        | JSON SETTINGS
        |--------------------------------------------------------------------------
        */


        $json = $settings->settings ?? [];



        $resources = $request->input(
            'settings.resources',
            []
        );



        $features = [


            'show_stock',

            'show_favorites',

            'show_rating',

            'show_sale_price',

            'show_related_products',

            'show_sold_products',

            'show_whatsapp_button',

            'show_share',

            'show_breadcrumbs',


        ];




        foreach($features as $feature){


            $resources[$feature] = 
                isset($resources[$feature]) &&
                $resources[$feature] == "1";


        }





        $json['resources'] = $resources;



        $settings->settings = $json;


        $settings->save();








        /*
        |--------------------------------------------------------------------------
        | Nome da loja pertence a tabela stores
        |--------------------------------------------------------------------------
        */


        if($request->filled('name')){


            $store->name = $request->name;


            $store->save();

        }





        return redirect()

            ->route('settings.edit')

            ->with(
                'success',
                'Configurações salvas.'
            );


    }







}