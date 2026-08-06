<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('store_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nome exibido pro cliente no checkout
            // (ex: "Retirar na loja física", "Sudeste - até 50kg")
            $table->string('name');

            // 'pickup' (retirada, sem cálculo de região/peso) ou
            // 'region' (aplica só pros estados marcados, dentro da
            // faixa de peso)
            $table->string('type')->default('region');

            // Lista de UFs (ex: ["SP","RJ","MG","ES"]) — null/vazio
            // quando type=pickup (não depende de destino).
            $table->json('states')->nullable();

            $table->decimal('min_weight', 8, 3)->default(0);

            // Nulo = sem limite superior de peso
            $table->decimal('max_weight', 8, 3)->nullable();

            $table->decimal('price', 10, 2)->default(0);

            $table->unsignedInteger('estimated_days')->nullable();

            $table->boolean('active')->default(true);

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rules');
    }
};
