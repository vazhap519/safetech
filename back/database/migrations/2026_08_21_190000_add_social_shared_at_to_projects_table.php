<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projects') || Schema::hasColumn('projects', 'social_shared_at')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table): void {
            $table->timestamp('social_shared_at')->nullable()->after('published_at')->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('projects') || ! Schema::hasColumn('projects', 'social_shared_at')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn('social_shared_at');
        });
    }
};
