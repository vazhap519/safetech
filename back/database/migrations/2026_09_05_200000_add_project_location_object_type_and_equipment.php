<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('city', 120)->nullable()->after('technology');
            $table->string('object_type', 160)->nullable()->after('city');
            $table->json('equipment')->nullable()->after('object_type');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn(['city', 'object_type', 'equipment']);
        });
    }
};
