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
            if (! Schema::hasColumn('posts', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }

            if (! Schema::hasColumn('posts', 'meta_description')) {
                $table->text('meta_description')->nullable();
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
                ['meta_title', 'meta_description'],
                fn (string $column): bool => Schema::hasColumn('posts', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
