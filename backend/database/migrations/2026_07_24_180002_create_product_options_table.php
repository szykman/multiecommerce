<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Ex: "Cor", "Tamanho", "Voltagem"
            $table->string('name');

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            // Não faz sentido o mesmo produto ter duas opções
            // com o mesmo nome (duas vezes "Cor", por exemplo)
            $table->unique(['product_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
