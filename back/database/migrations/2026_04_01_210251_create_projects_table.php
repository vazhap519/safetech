<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

                public function up(): void
                {
                    Schema::create('projects', function (Blueprint $table) {
                        $table->id();

                        /* =========================
                           BASIC
                        ========================= */
                        $table->string('title');
                        $table->string('slug')->unique();

                        $table->text('excerpt')->nullable();
                        $table->longText('content')->nullable(); // 🔥 future (details page)

                        /* =========================
                           RELATION
                        ========================= */
                        $table->foreignId('category_id')
                            ->nullable()
                            ->constrained('project_categories')
                            ->nullOnDelete();

                        /* =========================
                           VIDEO
                        ========================= */
                        $table->string('video_url')->nullable();

                        /* =========================
                           SEO
                        ========================= */
                        $table->json('seo')->nullable();

                        /* =========================
                           STATUS
                        ========================= */
                        $table->boolean('is_published')->default(true);
                        $table->timestamp('published_at')->nullable();

                        /* =========================
                           ORDERING
                        ========================= */
                        $table->integer('sort_order')->default(0);

                        /* =========================
                           TIMESTAMPS
                        ========================= */
                        $table->timestamps();

                        /* =========================
                           INDEXES
                        ========================= */
                        $table->index('slug');
                        $table->index('is_published');
                        $table->index('category_id');
                    });
                }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
