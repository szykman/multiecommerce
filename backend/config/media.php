<?php

/*
|--------------------------------------------------------------------------
| Whitelist de tipos de mídia permitidos
|--------------------------------------------------------------------------
|
| Cada entrada define, por extensão:
| - type: agrupamento usado no restante do sistema (image / video)
| - mimes: MIME(s) reais aceitos (validados via fileinfo, não pela
|          extensão declarada pelo navegador)
| - max_kb: tamanho máximo em KB para esse tipo de arquivo
|
*/

return [

    'allowed' => [

        'jpg' => [
            'type'   => 'image',
            'mimes'  => ['image/jpeg'],
            'max_kb' => 4096,
        ],

        'jpeg' => [
            'type'   => 'image',
            'mimes'  => ['image/jpeg'],
            'max_kb' => 4096,
        ],

        'bmp' => [
            'type'   => 'image',
            'mimes'  => ['image/bmp', 'image/x-ms-bmp'],
            'max_kb' => 4096,
        ],

        'png' => [
            'type'   => 'image',
            'mimes'  => ['image/png'],
            'max_kb' => 4096,
        ],

        'gif' => [
            'type'   => 'image',
            'mimes'  => ['image/gif'],
            'max_kb' => 8192,
        ],

        'mp4' => [
            'type'   => 'video',
            'mimes'  => ['video/mp4'],
            'max_kb' => 51200,
        ],

        'webm' => [
            'type'   => 'video',
            'mimes'  => ['video/webm'],
            'max_kb' => 51200,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Geração de thumbnail / preview
    |--------------------------------------------------------------------------
    |
    | Aplicado apenas a arquivos do tipo "image". Vídeos não têm
    | thumbnail automático nesta versão (exigiria ffmpeg).
    |
    */

    'thumbnail_width' => 300,

    'preview_width' => 1200,

    'output_format' => 'webp',

    'thumbnail_quality' => 80,

    'preview_quality' => 85,

];
