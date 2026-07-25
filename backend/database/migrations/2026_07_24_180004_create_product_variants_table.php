<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Código único da variante (ex: CAMISA-PRETO-M).
            // Nullable pois pode ser gerado depois ou não usado por
            // toda loja.
            $table->string('sku')->nullable();

            $table->decimal('price', 10, 2);

            $table->decimal('sale_price', 10, 2)->nullable();

            $table->unsignedInteger('stock')->default(0);

            $table->boolean('active')->default(true);

            $table->timestamps();

            // Índice normal (não único): SKU pode repetir entre lojas
            // diferentes, já que store_id não está denormalizado aqui.
            // A unicidade DENTRO da mesma loja é validada na aplicação
            // (ver ProductVariantController), consultando o SKU
            // escopado por products.store_id via join.
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
