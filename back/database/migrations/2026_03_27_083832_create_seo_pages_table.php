<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_pages', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | 🔥 HYBRID SEO SYSTEM
            |--------------------------------------------------------------------------
            */

            // static pages (home, services...)
            $table->string('key')->nullable()->index();

            // dynamic models (About, Post...)
            $table->nullableMorphs('seoable'); // seoable_id + seoable_type

            /*
            |--------------------------------------------------------------------------
            | URL
            |--------------------------------------------------------------------------
            */
            $table->string('slug')->nullable()->index();

            /*
            |--------------------------------------------------------------------------
            | BASIC SEO
            |--------------------------------------------------------------------------
            */
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('keywords')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SOCIAL SEO
            |--------------------------------------------------------------------------
            */
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | ADVANCED SEO
            |--------------------------------------------------------------------------
            */
            $table->string('canonical')->nullable()->index();
            $table->boolean('noindex')->default(false);
            $table->string('schema_type')->nullable(); // Article, WebPage, Service
            $table->json('schema')->nullable(); // custom override
            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | 🔥 CONSTRAINTS (IMPORTANT)
            |--------------------------------------------------------------------------
            */

            // static pages unique
            $table->unique('key');

            // one SEO per model
            $table->unique(['seoable_id', 'seoable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};
