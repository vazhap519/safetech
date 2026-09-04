<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seed_deletion_tombstones')) {
            return;
        }

        Schema::create('seed_deletion_tombstones', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 64);
            // Canonical identifiers are short. Keeping this index-safe for
            // older utf8mb4 MySQL installations avoids a failed deployment.
            $table->string('key', 191);
            $table->timestamps();

            $table->unique(['type', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seed_deletion_tombstones');
    }
};
