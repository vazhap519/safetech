<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->string('submission_key', 128)->nullable()->unique();
            $table->string('submission_payload_hash', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->dropUnique(['submission_key']);
            $table->dropColumn(['submission_key', 'submission_payload_hash']);
        });
    }
};
