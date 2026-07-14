<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->decimal('sale_price', 10, 2)
                ->nullable()
                ->after('price');

            $table->dateTime('promotion_start')
                ->nullable()
                ->after('sale_price');

            $table->dateTime('promotion_end')
                ->nullable()
                ->after('promotion_start');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'sale_price',
                'promotion_start',
                'promotion_end',
            ]);

        });
    }
};

