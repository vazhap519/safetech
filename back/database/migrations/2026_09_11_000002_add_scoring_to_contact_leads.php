<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->unsignedTinyInteger('lead_score')->default(0)->after('status');
            $table->string('priority', 16)->default('normal')->after('lead_score');
            $table->index(['priority', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->dropIndex(['priority', 'status', 'created_at']);
            $table->dropColumn(['lead_score', 'priority']);
        });
    }
};
