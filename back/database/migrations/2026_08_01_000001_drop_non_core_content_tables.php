<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('privacy_policies');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Removed modules are intentionally not recreated.
    }
};
