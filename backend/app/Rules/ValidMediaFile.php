<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Valida um arquivo enviado contra a whitelist definida em config/media.php.
 *
 * Camadas de checagem (defesa em profundidade — cada uma cobre o
 * que a anterior poderia deixar passar):
 *
 * 1. Extensão precisa estar na whitelist.
 * 2. MIME real do conteúdo (fileinfo, não o que o navegador declarou)
 *    precisa bater com o esperado para aquela extensão.
 * 3. Tamanho máximo, diferenciado por tipo de arquivo.
 * 4. Para imagens: tentativa real de decodificação. Um arquivo
 *    disfarçado (ex: renomeado para .jpg) que porventura engane a
 *    checagem de MIME ainda é barrado aqui, pois não decodifica
 *    como imagem válida.
 */
class ValidMediaFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('Arquivo inválido.');
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        $allowed = config('media.allowed', []);

        if (! array_key_exists($extension, $allowed)) {
            $fail('Formato não permitido. Envie apenas: '.implode(', ', array_keys($allowed)).'.');
            return;
        }

        $rules = $allowed[$extension];

        $realMime = $value->getMimeType();

        if (! in_array($realMime, $rules['mimes'], true)) {
            $fail('O conteúdo do arquivo não corresponde à extensão informada.');
            return;
        }

        $sizeKb = $value->getSize() / 1024;

        if ($sizeKb > $rules['max_kb']) {
            $fail('Arquivo excede o tamanho máximo permitido para este tipo ('.$rules['max_kb'].' KB).');
            return;
        }

        if ($rules['type'] === 'image') {

            $info = @getimagesize($value->getRealPath());

            if ($info === false) {
                $fail('O arquivo não é uma imagem válida.');
                return;
            }
        }
    }
}
