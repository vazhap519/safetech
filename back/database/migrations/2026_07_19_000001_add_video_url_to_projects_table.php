<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projects') || Schema::hasColumn('projects', 'video_url')) {
            return;
        }

        $afterColumn = Schema::hasColumn('projects', 'image_alt')
            ? 'image_alt'
            : null;

        Schema::table('projects', function (Blueprint $table) use ($afterColumn): void {
            $column = $table->string('video_url', 2048)->nullable();

            if ($afterColumn) {
                $column->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('projects') || ! Schema::hasColumn('projects', 'video_url')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn('video_url');
        });
    }
};
