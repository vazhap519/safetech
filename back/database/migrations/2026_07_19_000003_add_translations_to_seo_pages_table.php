<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_pages') || Schema::hasColumn('seo_pages', 'translations')) {
            return;
        }

        Schema::table('seo_pages', function (Blueprint $table): void {
            $table->json('translations')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('seo_pages') || ! Schema::hasColumn('seo_pages', 'translations')) {
            return;
        }

        Schema::table('seo_pages', function (Blueprint $table): void {
            $table->dropColumn('translations');
        });
    }
};
