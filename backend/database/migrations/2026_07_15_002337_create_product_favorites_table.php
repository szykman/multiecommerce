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
Schema::create('product_favorites', function (Blueprint $table) {

    $table->id();

    $table->foreignId('store_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('product_id')
          ->constrained()
          ->cascadeOnDelete();


    // visitante sem login
    $table->string('session_id')
          ->nullable();


    // cliente logado futuro
    $table->foreignId('user_id')
          ->nullable();


    $table->timestamps();


    $table->unique([
        'product_id',
        'session_id'
    ]);

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_favorites');
    }
};
