<?php

namespace Tests\Feature;

use App\Models\SeoPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_localized_admin_managed_metadata(): void
    {
        SeoPage::query()->create([
            'key' => 'home',
            'slug' => '/',
            'title' => 'ქართული სათაური',
            'description' => 'ქართული აღწერა',
            'keywords' => [['value' => 'ქართული']],
            'noindex' => false,
            'schema_type' => 'WebPage',
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Admin schema',
            ],
            'translations' => [
                'fields' => [
                    'title' => ['ka' => 'ქართული სათაური', 'en' => 'English title', 'ru' => 'Русский заголовок'],
                    'description' => ['ka' => 'ქართული აღწერა', 'en' => 'English description', 'ru' => 'Русское описание'],
                ],
                'keywords' => ['ru' => ['ИТ', 'безопасность']],
            ],
        ]);

        $this->getJson('/api/seo/home?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.title', 'Русский заголовок')
            ->assertJsonPath('data.description', 'Русское описание')
            ->assertJsonPath('data.keywords.0', 'ИТ')
            ->assertJsonPath('data.schemaOverride.name', 'Admin schema')
            ->assertJsonPath('data.robots', 'index, follow');
    }

    public function test_it_returns_not_found_without_leaking_an_exception(): void
    {
        $this->getJson('/api/seo/missing-page')
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'SEO page not found.',
            ]);
    }

    public function test_index_returns_only_lightweight_sitemap_fields(): void
    {
        SeoPage::query()->create([
            'key' => 'services',
            'slug' => '/services',
            'title' => 'Services',
            'description' => 'Managed services metadata.',
            'noindex' => false,
        ]);

        $this->getJson('/api/seo')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'key',
                    'slug',
                    'title',
                    'noindex',
                    'updated_at',
                ]],
            ])
            ->assertJsonMissingPath('data.0.meta')
            ->assertJsonMissingPath('data.0.og_image_url')
            ->assertJsonMissingPath('data.0.share_image_url');
    }
}
