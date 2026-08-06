<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {

            // CEP de onde a loja despacha os produtos — necessário
            // pro cálculo dos Correios (parâmetro cepOrigem da API),
            // mesmo usando o contrato único da plataforma.
            $table->string('origin_zipcode', 9)->nullable();

            $table->boolean('correios_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['origin_zipcode', 'correios_enabled']);
        });
    }
};
