<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {

            // 🔥 FAQ (Google rich results)
            $table->json('faq')->nullable();

            // 🔥 Custom schema (override)
            $table->json('schema')->nullable();

            // 🔥 SEO author override
            $table->string('seo_author')->nullable();

            // 🔥 SEO publish date
            $table->timestamp('seo_published_at')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'faq',
                'schema',
                'seo_author',
                'seo_published_at',
            ]);
        });
    }
};
