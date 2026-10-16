<?php

use Database\Seeders\TbilisiCctvLandingSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(TbilisiCctvLandingSeeder::class)->run();
    }

    public function down(): void
    {
        // Editorial changes made after deployment must not be overwritten by a
        // rollback. The original record is retained in private content-backups.
    }
};
