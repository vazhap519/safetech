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
            if (Schema::hasColumn('services', 'short_description')) {
                $table->text('short_description')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
