<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pages', 'local_service_landings'] as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'schema')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->json('schema')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['pages', 'local_service_landings'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'schema')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('schema');
            });
        }
    }
};
