<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->longText('intro_text')->nullable();
            $table->json('faq')->nullable();
            $table->json('schema')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::create('product_filters', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('translations')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_category_id')
                ->constrained('product_categories')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('description');
            $table->string('image_alt')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('GEL');
            $table->json('filter_values')->nullable();
            $table->json('seo')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_filters');
        Schema::dropIfExists('product_categories');
    }
};
