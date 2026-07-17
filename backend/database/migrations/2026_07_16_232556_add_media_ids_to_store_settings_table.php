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
        Schema::table('store_settings', function (Blueprint $table) {

            $table->foreignId('logo_media_id')
                ->nullable()
                ->after('logo')
                ->constrained('media')
                ->nullOnDelete();

            $table->foreignId('banner_media_id')
                ->nullable()
                ->after('banner')
                ->constrained('media')
                ->nullOnDelete();

            $table->foreignId('favicon_media_id')
                ->nullable()
                ->after('favicon')
                ->constrained('media')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {

            $table->dropForeign(['logo_media_id']);
            $table->dropForeign(['banner_media_id']);
            $table->dropForeign(['favicon_media_id']);

            $table->dropColumn([
                'logo_media_id',
                'banner_media_id',
                'favicon_media_id'
            ]);

        });
    }
};
