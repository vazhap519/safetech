<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Database\Seeders\LocalSeoFieldCompletionSeeder;
use Database\Seeders\LocalServiceCoverageSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalSeoFieldCompletionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fills_missing_trilingual_fields_and_repeaters_without_creating_city_pages(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(LocalServiceCoverageSeeder::class);

        $landing = LocalServiceLanding::query()
            ->where('location_slug', 'tbilisi')
            ->whereHas('service', fn ($query) => $query->where('slug', 'ip-camera-installation'))
            ->sole();

        $this->assertEmpty($landing->benefits);
        $this->assertEmpty($landing->faq);
        $existingCount = LocalServiceLanding::query()->count();
        $customTitle = 'რედაქტორის მიერ შექმნილი SEO სათაური';
        $landing->fill([
            'seo_title' => $customTitle,
            'noindex' => true,
            'is_published' => false,
            'translations' => ['fields' => ['seoTitle' => ['en' => 'Editor English headline']]],
        ])->save();

        $this->seed(LocalSeoFieldCompletionSeeder::class);
        $this->seed(LocalSeoFieldCompletionSeeder::class);

        $landing->refresh();
        $this->assertDatabaseCount('local_service_landings', $existingCount);
        $this->assertSame($customTitle, $landing->seo_title);
        $this->assertSame('Editor English headline', data_get($landing->translations, 'fields.seoTitle.en'));
        $this->assertTrue($landing->noindex);
        $this->assertFalse($landing->is_published);
        $this->assertCount(2, $landing->benefits);
        $this->assertCount(2, $landing->faq);
        $this->assertCount(0, $landing->projects);

        foreach (['ka', 'en', 'ru'] as $locale) {
            foreach ([
                'locationName', 'eyebrow', 'title', 'excerpt', 'content',
                'ctaTitle', 'ctaText', 'primaryKeyword', 'seoTitle',
                'seoDescription', 'ogTitle', 'ogDescription',
            ] as $field) {
                $this->assertNotEmpty(
                    data_get($landing->translations, "fields.{$field}.{$locale}"),
                    "{$field}.{$locale}"
                );
            }

            $this->assertNotEmpty($locale === 'ka' ? $landing->keywords : data_get($landing->translations, "keywords.{$locale}"));
        }
        foreach ($landing->benefits as $benefit) {
            foreach (['en', 'ru'] as $locale) {
                $this->assertNotEmpty(data_get($benefit, "translations.{$locale}.title"));
                $this->assertNotEmpty(data_get($benefit, "translations.{$locale}.description"));
            }
        }
        foreach ($landing->faq as $faq) {
            foreach (['en', 'ru'] as $locale) {
                $this->assertNotEmpty(data_get($faq, "translations.{$locale}.question"));
                $this->assertNotEmpty(data_get($faq, "translations.{$locale}.answer"));
            }
        }
    }

    public function test_it_preserves_custom_blocks_and_does_not_expand_unverified_locations(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $service = Service::query()->where('slug', 'barrier-gate-installation')->sole();
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'surami',
            'location_name' => 'სურამი',
            'title' => 'სურამში რეალური პროექტის სათაური',
            'content' => 'ავტორის მიერ მომზადებული ტექსტი.',
            'benefits' => [['title' => 'CMS custom', 'description' => 'Keep this unchanged.']],
            'faq' => [['question' => 'CMS FAQ', 'answer' => 'Keep this unchanged.']],
            'noindex' => true,
            'is_published' => false,
        ]);

        $this->seed(LocalSeoFieldCompletionSeeder::class);
        $landing->refresh();

        $this->assertSame('სურამში რეალური პროექტის სათაური', $landing->title);
        $this->assertSame('ავტორის მიერ მომზადებული ტექსტი.', $landing->content);
        $this->assertSame('CMS custom', $landing->benefits[0]['title']);
        $this->assertSame('CMS FAQ', $landing->faq[0]['question']);
        $this->assertTrue($landing->noindex);
        $this->assertFalse($landing->is_published);
        $this->assertSame('Surami', data_get($landing->translations, 'fields.locationName.en'));
        $this->assertStringContainsString('Surami', data_get($landing->translations, 'fields.content.en'));
        $this->assertDatabaseCount('local_service_landings', 1);
    }
}
