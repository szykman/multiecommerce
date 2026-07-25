<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Indica se o estoque/preço do produto vem das variantes
            // (calculado automaticamente) ou é controlado manualmente
            // (comportamento atual, sem variação).
            $table->boolean('has_variants')
                ->default(false)
                ->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('has_variants');
        });
    }
};
