<?php

use App\Support\Seo\CanonicalLocalSeo;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        CanonicalLocalSeo::prune();
    }

    public function down(): void
    {
        // Deliberately irreversible: only legacy, duplicate or unsupported
        // local pages are removed by this migration.
    }
};
