<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {

            $table->id();

            $table->foreignId('store_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('file');

            $table->string('type')->index();

            $table->string('mime')->nullable();

            $table->string('extension',20)->nullable();

            $table->unsignedBigInteger('size')->default(0);

            $table->integer('width')->nullable();

            $table->integer('height')->nullable();

            $table->string('folder')->default('Geral');

            $table->string('alt')->nullable();

            $table->text('caption')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
