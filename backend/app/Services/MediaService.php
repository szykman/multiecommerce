<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class MediaService
{
    protected ImageManager $imageManager;

    public function __construct()
    {
      //  $this->imageManager = new ImageManager(new Driver());
$this->imageManager = ImageManager::usingDriver(Driver::class);
    }

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
        | Hash do arquivo (evita duplicidade por conteúdo, escopado por loja)
        |--------------------------------------------------------------------------
        */

        $hash = sha1_file($file->getRealPath());

        $existing = Media::where('store_id', $storeId)
            ->where('metadata->hash', $hash)
            ->first();

        if ($existing) {
            return $existing;
        }

        /*
        |--------------------------------------------------------------------------
        | Nome original / nome físico
        |--------------------------------------------------------------------------
        */

        $originalName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $filename = Str::uuid().'.'.$file->extension();

        $path = $file->storeAs('media', $filename, 'public');

        /*
        |--------------------------------------------------------------------------
        | Tipo / MIME
        |--------------------------------------------------------------------------
        */

        $mime = $file->getMimeType();
        $type = explode('/', $mime)[0];

        /*
        |--------------------------------------------------------------------------
        | Dimensões (apenas imagens)
        |--------------------------------------------------------------------------
        */

        $width = null;
        $height = null;

        if ($type === 'image') {

            try {
                $size = getimagesize($file->getRealPath());

                if ($size) {
                    $width = $size[0];
                    $height = $size[1];
                }
            } catch (\Throwable $e) {
                // dimensões ficam nulas, não impede o upload
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
                'hash' => $hash,
            ],

        ]);

        $this->generateDerivatives($media);

        return $media;
    }

    /**
     * Gera thumbnail (300px) e preview (1200px) em WEBP, sem EXIF.
     * O arquivo original nunca é alterado.
     */
    protected function generateDerivatives(Media $media): void
    {
        if ($media->type !== 'image') {
            return;
        }

        $originalFullPath = Storage::disk('public')->path($media->file);

        $format = config('media.output_format', 'webp');

        try {

            $thumbRelative = 'media/thumbs/'.Str::uuid().'.'.$format;
            $thumbFullPath = Storage::disk('public')->path($thumbRelative);
            $this->ensureDirectoryExists($thumbFullPath);

    $thumbImage = $this->imageManager
    ->decodePath($originalFullPath)
    ->scaleDown(width: config('media.thumbnail_width',300));

            $this->stripMetadata($thumbImage);

            $thumbImage->save($thumbFullPath, quality: config('media.thumbnail_quality', 80));

            $previewRelative = 'media/previews/'.Str::uuid().'.'.$format;
            $previewFullPath = Storage::disk('public')->path($previewRelative);
            $this->ensureDirectoryExists($previewFullPath);

          //  $previewImage = $this->imageManager
            //    ->read($originalFullPath)
              //  ->scaleDown(width: config('media.preview_width', 1200));

$previewImage = $this->imageManager
    ->decodePath($originalFullPath)
    ->scaleDown(width: config('media.preview_width',1200));


            $this->stripMetadata($previewImage);

            $previewImage->save($previewFullPath, quality: config('media.preview_quality', 85));

            $media->thumbnail = $thumbRelative;
            $media->preview = $previewRelative;
            $media->optimized = true;

            $media->save();

        } catch (\Throwable $e) {

            // Falha na geração não deve impedir o upload do arquivo
            // original. Usamos o original como fallback nos dois
            // campos, e registramos o erro para investigação.
            $media->thumbnail = $media->file;
            $media->preview = $media->file;
            $media->optimized = false;

            $media->save();

            Log::warning('Falha ao gerar thumbnail/preview de mídia', [
                'media_id' => $media->id,
                'store_id' => $media->store_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Regenera thumbnail/preview de uma mídia já existente
     * (usado pelo comando de backfill em uploads antigos).
     */
    public function regenerate(Media $media): void
    {
        $this->generateDerivatives($media);
    }

    /**
     * Remove metadados EXIF (incluindo GPS) da imagem antes de salvar.
     * Relevante para privacidade em contexto multi-tenant: fotos de
     * celular frequentemente carregam coordenadas de onde foram
     * tiradas, e isso não deve vazar nos thumbnails/previews públicos.
     *
     * A orientação (rotação) já foi aplicada aos pixels na leitura
     * (o driver Imagick do Intervention Image auto-orienta ao ler),
     * então removê-la depois não afeta a exibição correta da imagem.
     */
    protected function stripMetadata($image): void
    {
        try {
            $image->core()->native()->stripImage();
        } catch (\Throwable $e) {
            // Se não for possível limpar os metadados, seguimos sem
            // interromper a geração — não é um erro fatal.
        }
    }

    protected function ensureDirectoryExists(string $fullPath): void
    {
        $directory = dirname($fullPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    public function url(Media $media): string
    {
        return asset('storage/'.$media->file);
    }

    public function preview(Media $media): string
    {
        return asset('storage/'.($media->preview ?: $media->file));
    }

    public function thumbnail(Media $media): string
    {
        return asset('storage/'.($media->thumbnail ?: $media->file));
    }

    /**
     * Remove mídia e todos os seus derivados (original, thumbnail, preview).
     */
    public function delete(Media $media): void
    {
        foreach (['file', 'thumbnail', 'preview'] as $field) {

            $value = $media->{$field};

            if ($value && Storage::disk('public')->exists($value)) {
                Storage::disk('public')->delete($value);
            }
        }

        $media->delete();
    }
}
