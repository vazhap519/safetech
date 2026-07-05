<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('eyebrow')->nullable();
            $table->string('icon')->default('settings');
            $table->string('title');
            $table->text('description');
            $table->text('seo_description');
            $table->string('hero_image')->nullable();
            $table->json('keywords')->nullable();
            $table->json('highlights')->nullable();
            $table->json('overview')->nullable();
            $table->json('benefits')->nullable();
            $table->json('solutions')->nullable();
            $table->json('industries')->nullable();
            $table->json('process')->nullable();
            $table->json('brands')->nullable();
            $table->text('warranty')->nullable();
            $table->text('sla')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('title');
            $table->text('description');
            $table->text('seo_description');
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('category')->default('offices')->index();
            $table->string('technology')->nullable();
            $table->string('icon')->default('business');
            $table->string('accent', 20)->default('primary');
            $table->json('meta')->nullable();
            $table->json('scope')->nullable();
            $table->json('specs')->nullable();
            $table->json('challenges')->nullable();
            $table->json('solutions')->nullable();
            $table->json('process')->nullable();
            $table->json('gallery')->nullable();
            $table->json('results')->nullable();
            $table->json('testimonial')->nullable();
            $table->json('related')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('position');
            $table->string('image')->nullable();
            $table->text('bio')->nullable();
            $table->json('socials')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->string('category')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->text('quote');
            $table->string('author');
            $table->string('role')->nullable();
            $table->string('company')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('context')->default('general')->index();
            $table->text('question');
            $table->text('answer');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general')->index();
            $table->json('value')->nullable();
            $table->boolean('is_public')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(false)->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
    }
};
