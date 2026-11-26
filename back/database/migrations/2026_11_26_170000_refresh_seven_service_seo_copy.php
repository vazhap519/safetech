<?php

use Database\Seeders\PageContentSeeder;
use Database\Seeders\SeoPageSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new SeoPageSeeder)->refreshCoreSeoPages();
        (new PageContentSeeder)->refreshSevenServiceCopy();
    }

    public function down(): void
    {
        // Editorial SEO copy changes are intentionally not rolled back.
    }
};
