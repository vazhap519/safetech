<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'short_description')) {
                $table->text('short_description')->nullable();
            }

            if (! Schema::hasColumn('services', 'long_description')) {
                $table->longText('long_description')->nullable();
            }

            if (! Schema::hasColumn('services', 'seo')) {
                $table->json('seo')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            foreach (['seo', 'long_description', 'short_description'] as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
