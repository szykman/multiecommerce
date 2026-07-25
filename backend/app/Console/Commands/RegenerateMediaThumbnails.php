<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Console\Command;

class RegenerateMediaThumbnails extends Command
{
    /**
     * Uso:
     *   php artisan media:generate-thumbnails
     *   php artisan media:generate-thumbnails --store_id=3
     *   php artisan media:generate-thumbnails --only-missing
     */
    protected $signature = 'media:generate-thumbnails
        {--store_id= : Processa apenas mídias de uma loja específica}
        {--only-missing : Processa apenas mídias ainda não otimizadas (optimized = false)}';

    protected $description = 'Gera/regenera thumbnail (300px) e preview (1200px) para imagens já existentes na biblioteca';

    public function handle(MediaService $service)
    {
        $query = Media::where('type', 'image');

        if ($this->option('store_id')) {
            $query->where('store_id', $this->option('store_id'));
        }

        if ($this->option('only-missing')) {
            $query->where(function ($q) {
                $q->where('optimized', false)
                  ->orWhereNull('optimized');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Nenhuma mídia para processar.');
            return self::SUCCESS;
        }

        $this->info("Processando {$total} imagem(ns)...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $falhas = [];

        $query->chunkById(50, function ($chunk) use ($service, $bar, &$falhas) {

            foreach ($chunk as $media) {

                try {
                    $service->regenerate($media);
                } catch (\Throwable $e) {
                    $falhas[] = $media->id.' — '.$e->getMessage();
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        if (! empty($falhas)) {

            $this->warn(count($falhas).' mídia(s) falharam:');

            foreach ($falhas as $falha) {
                $this->line('  - '.$falha);
            }

        } else {

            $this->info('Concluído sem falhas.');
        }

        return self::SUCCESS;
    }
}
