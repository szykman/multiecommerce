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

Schema::table('media', function (Blueprint $table) {

    $table->string('thumbnail')->nullable()->after('file');

    $table->string('preview')->nullable()->after('thumbnail');

    $table->string('title')->nullable()->after('name');

    $table->json('metadata')->nullable()->after('caption');

    $table->boolean('optimized')
        ->default(false)
        ->after('metadata');

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            //
        });
    }
};
