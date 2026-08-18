<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceWithNullOnDelete(
            'services',
            'category_for_service_id',
            'category_for_services',
        );
        $this->replaceWithNullOnDelete(
            'projects',
            'category_id',
            'project_categories',
        );
    }

    public function down(): void
    {
        // The repaired constraints are intentionally retained. Reintroducing
        // restrictive production constraints would make admin deletes fail.
    }

    private function replaceWithNullOnDelete(string $table, string $column, string $references): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column): void {
            $blueprint->dropForeign([$column]);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column, $references): void {
            $blueprint->foreign($column)
                ->references('id')
                ->on($references)
                ->nullOnDelete();
        });
    }
};
