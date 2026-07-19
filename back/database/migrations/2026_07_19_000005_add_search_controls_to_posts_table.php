<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table): void {
            if (! Schema::hasColumn('posts', 'seo_keywords')) {
                $table->json('seo_keywords')->nullable();
            }

            if (! Schema::hasColumn('posts', 'noindex')) {
                $table->boolean('noindex')->default(false)->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table): void {
            $columns = array_values(array_filter(
                ['seo_keywords', 'noindex'],
                fn (string $column): bool => Schema::hasColumn('posts', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
