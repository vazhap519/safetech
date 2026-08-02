<?php

namespace Tests\Feature;

use App\Models\SeoPage;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\PrivacyPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyPageSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_multilingual_privacy_page_idempotently(): void
    {
        $this->seed(PrivacyPageSeeder::class);
        $this->seed(PrivacyPageSeeder::class);

        $seo = SeoPage::query()->where('key', 'privacy')->firstOrFail();

        $this->assertSame('/privacy', $seo->slug);
        $this->assertTrue($seo->noindex);
        $this->assertSame('WebPage', $seo->schema_type);
        $this->assertNotEmpty(data_get($seo->translations, 'fields.title.en'));
        $this->assertNotEmpty(data_get($seo->translations, 'fields.description.ru'));

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $map = MultilingualContent::mapFrom($translations->value);

        $this->assertSame('Privacy Policy', $map['privacy.title']['en'] ?? null);
        $this->assertSame(
            'Политика конфиденциальности',
            $map['privacy.title']['ru'] ?? null,
        );
        $this->assertNotEmpty($map['privacy.cookies.body']['ka'] ?? null);
    }
}
