<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyModuleRemovalMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_legacy_tables_seo_pages_and_translation_keys(): void
    {
        foreach (['authors', 'categories', 'posts', 'post_sections', 'product_categories', 'product_filters', 'products'] as $table) {
            Schema::create($table, function (Blueprint $blueprint): void {
                $blueprint->id();
            });
        }

        DB::table('seo_pages')->insert([
            [
                'key' => 'blog',
                'slug' => '/blog',
                'title' => 'Legacy',
                'description' => 'Legacy content',
                'schema_type' => 'WebPage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'translations'],
            [
                'value' => json_encode([
                    'entries' => [
                        ['key' => 'nav.blog', 'ka' => 'ბლოგი'],
                        ['key' => 'shop.hero.title', 'ka' => 'მაღაზია'],
                        ['key' => 'nav.home', 'ka' => 'მთავარი'],
                    ],
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        $migration = require database_path('migrations/2026_09_01_000001_remove_legacy_blog_and_shop_data.php');
        $migration->up();

        foreach (['authors', 'categories', 'posts', 'post_sections', 'product_categories', 'product_filters', 'products'] as $table) {
            $this->assertFalse(Schema::hasTable($table));
        }

        $this->assertDatabaseMissing('seo_pages', ['key' => 'blog']);

        $translations = json_decode(
            (string) DB::table('site_settings')->where('key', 'translations')->value('value'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $keys = collect($translations['entries'])->pluck('key')->all();

        $this->assertSame(['nav.home'], $keys);
    }
}
