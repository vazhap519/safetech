<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['category_for_services', 'project_categories'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->boolean('is_published')->default(true)->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['category_for_services', 'project_categories'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('is_published');
            });
        }
    }
};
