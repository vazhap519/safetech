<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_catalog_items', function (Blueprint $table): void {
            $table->json('technical_specs')->nullable()->after('warranty_months');
        });
    }

    public function down(): void
    {
        Schema::table('quote_catalog_items', function (Blueprint $table): void {
            $table->dropColumn('technical_specs');
        });
    }
};
