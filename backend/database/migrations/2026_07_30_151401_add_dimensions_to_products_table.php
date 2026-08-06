<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Peso em kg, dimensões em cm — usados no cálculo de
            // frete dos Correios. Nulos = produto ainda não tem
            // essas medidas cadastradas (cai no frete fixo por
            // região, se configurado, ou fica sem opção de Correios).
            $table->decimal('weight', 8, 3)->nullable()->after('stock');
            $table->decimal('height', 8, 2)->nullable()->after('weight');
            $table->decimal('width', 8, 2)->nullable()->after('height');
            $table->decimal('length', 8, 2)->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'height', 'width', 'length']);
        });
    }
};
