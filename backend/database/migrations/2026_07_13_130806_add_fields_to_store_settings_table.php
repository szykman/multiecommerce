<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {

            $table->string('email')->nullable()->after('settings');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('hours')->nullable();

            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('favicon')->nullable();

            $table->string('primary_color')->default('#0d6efd');
            $table->string('secondary_color')->default('#6c757d');
            $table->integer('radius')->default(10);

            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->longText('google_maps')->nullable();
            $table->string('copyright')->nullable();
            $table->longText('footer_text')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {

            $table->dropColumn([
                'email',
                'phone',
                'whatsapp',
                'hours',
                'logo',
                'banner',
                'favicon',
                'primary_color',
                'secondary_color',
                'radius',
                'instagram',
                'facebook',
                'youtube',
                'tiktok',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'google_maps',
                'copyright',
                'footer_text'
            ]);

        });
    }
};

