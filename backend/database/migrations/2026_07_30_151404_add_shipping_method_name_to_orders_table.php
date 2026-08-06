<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Snapshot do nome da opção de frete escolhida (ex:
            // "Correios PAC", "Retirar na loja física") — o preço já
            // existe no campo shipping_cost, isso é só o rótulo.
            $table->string('shipping_method_name')->nullable()->after('shipping_cost');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_method_name');
        });
    }
};
