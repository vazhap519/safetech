<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_service_landings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('location_slug', 120);
            $table->string('location_name');
            $table->string('title');
            $table->string('eyebrow')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cta_title')->nullable();
            $table->text('cta_text')->nullable();
            $table->json('benefits')->nullable();
            $table->json('faq')->nullable();
            $table->string('primary_keyword')->nullable();
            $table->json('keywords')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('noindex')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['service_id', 'location_slug'], 'local_landing_service_location_unique');
            $table->index(['location_slug', 'is_published'], 'local_landing_location_published_index');
        });

        Schema::create('local_service_landing_project', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('landing_id')
                ->constrained('local_service_landings')
                ->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['landing_id', 'project_id'], 'local_landing_project_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_service_landing_project');
        Schema::dropIfExists('local_service_landings');
    }
};
