<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_option_values', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_option_id')
                ->constrained()
                ->cascadeOnDelete();

            // Ex: "Preto", "M", "220V"
            $table->string('value');

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            // Não permite duplicar o mesmo valor dentro da mesma opção
            $table->unique(['product_option_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
