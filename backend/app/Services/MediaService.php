<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Faz upload do arquivo para a Biblioteca.
     */
    public function store(
        UploadedFile $file,
        int $storeId,
        string $folder = 'Geral'
    ): Media {

        /*
        |--------------------------------------------------------------------------
        | Hash do arquivo
        |--------------------------------------------------------------------------
        */

        $hash = sha1_file(
            $file->getRealPath()
        );

        /*
        |--------------------------------------------------------------------------
        | Evita duplicidade
        |--------------------------------------------------------------------------
        */

        $existing = Media::where(
            'store_id',
            $storeId
        )
        ->where(
            'metadata->hash',
            $hash
        )
        ->first();

        if ($existing) {

            return $existing;

        }

        /*
        |--------------------------------------------------------------------------
        | Nome original
        |--------------------------------------------------------------------------
        */

        $originalName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        /*
        |--------------------------------------------------------------------------
        | Nome físico
        |--------------------------------------------------------------------------
        */

        $filename =

            Str::uuid()

            .'.'

            .$file->extension();

        /*
        |--------------------------------------------------------------------------
        | Salva arquivo
        |--------------------------------------------------------------------------
        */

        $path = $file->storeAs(

            'media',

            $filename,

            'public'

        );

        /*
        |--------------------------------------------------------------------------
        | Tipo
        |--------------------------------------------------------------------------
        */

        $mime = $file->getMimeType();

        $type = explode(
            '/',
            $mime
        )[0];

        /*
        |--------------------------------------------------------------------------
        | Dimensões
        |--------------------------------------------------------------------------
        */

        $width = null;

        $height = null;

        if ($type == 'image') {

            try{

                $size = getimagesize(
                    $file->getRealPath()
                );

                if($size){

                    $width = $size[0];

                    $height = $size[1];

                }

            }catch(\Throwable $e){

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Registro
        |--------------------------------------------------------------------------
        */

        $media = Media::create([

            'store_id' => $storeId,

            'name' => $originalName,

            'title' => $originalName,

            'file' => $path,

            'type' => $type,

            'mime' => $mime,

            'extension' => $file->extension(),

            'size' => $file->getSize(),

            'width' => $width,

            'height' => $height,

            'folder' => $folder,

            'metadata' => [

                'hash' => $hash

            ]

        ]);

        /*
        |--------------------------------------------------------------------------
        | Próxima etapa
        |--------------------------------------------------------------------------
        */

        $this->generatePreview(
            $media
        );

        return $media;
    }

    /**
     * Gera preview e thumbnail.
     */
    protected function generatePreview(
        Media $media
    ): void
    {

        /*
        |--------------------------------------------------------------------------
        | Por enquanto apenas imagens.
        |--------------------------------------------------------------------------
        */

        if ($media->type !== 'image') {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Nesta primeira versão o preview
        | será o próprio arquivo original.
        |--------------------------------------------------------------------------
        */

        $media->preview = $media->file;

        $media->thumbnail = $media->file;

        $media->optimized = true;

        $media->save();

    }

    /**
     * Retorna URL pública.
     */
    public function url(
        Media $media
    ): string
    {

        return asset(
            'storage/'.$media->file
        );

    }

    /**
     * Retorna preview.
     */
    public function preview(
        Media $media
    ): string
    {

        if ($media->preview) {

            return asset(
                'storage/'.$media->preview
            );

        }

        return asset(
            'storage/'.$media->file
        );

    }

    /**
     * Retorna thumbnail.
     */
    public function thumbnail(
        Media $media
    ): string
    {

        if ($media->thumbnail) {

            return asset(
                'storage/'.$media->thumbnail
            );

        }

        return asset(
            'storage/'.$media->file
        );

    }

    /**
     * Remove mídia.
     */
    public function delete(
        Media $media
    ): void
    {

        if (

            Storage::disk('public')
                ->exists($media->file)

        ) {

            Storage::disk('public')
                ->delete($media->file);

        }

        if (

            $media->thumbnail &&

            Storage::disk('public')
                ->exists($media->thumbnail)

        ) {

            Storage::disk('public')
                ->delete($media->thumbnail);

        }

        if (

            $media->preview &&

            Storage::disk('public')
                ->exists($media->preview)

        ) {

            Storage::disk('public')
                ->delete($media->preview);

        }

        $media->delete();

    }

}
