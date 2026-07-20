<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_pages') || ! Schema::hasColumn('seo_pages', 'schema_type')) {
            return;
        }

        foreach ([
            'services' => 'CollectionPage',
            'service-calculator' => 'WebApplication',
            'projects' => 'CollectionPage',
            'about' => 'AboutPage',
            'blog' => 'Blog',
        ] as $key => $schemaType) {
            DB::table('seo_pages')
                ->where('key', $key)
                ->where(function ($query): void {
                    $query->whereNull('schema_type')->orWhere('schema_type', 'WebPage');
                })
                ->update([
                    'schema_type' => $schemaType,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Schema type upgrades are content-safe and intentionally retained.
    }
};
