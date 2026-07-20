<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\PrivacyPolicy;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_data_contains_calculators_translations_and_static_seo(): void
    {
        $this->seed(DatabaseSeeder::class);

        $existingCategory = CategoryForService::query()
            ->where('slug', 'security-systems')
            ->firstOrFail();
        $existingTranslations = $existingCategory->translations ?? [];
        data_set($existingTranslations, 'fields.seo_title.en', 'Admin-managed security title');
        $existingCategory->forceFill([
            'seo_title' => 'ადმინიდან შეცვლილი სათაური',
            'translations' => $existingTranslations,
        ])->save();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(5, Service::query()->published()->count());
        $this->assertSame(4, CategoryForService::query()->count());
        $this->assertSame(0, Service::query()->whereNull('category_for_service_id')->count());
        $this->assertSame(
            'ადმინიდან შეცვლილი სათაური',
            CategoryForService::query()->where('slug', 'security-systems')->value('seo_title'),
        );
        $this->assertSame(
            'Admin-managed security title',
            data_get(
                CategoryForService::query()
                    ->where('slug', 'security-systems')
                    ->firstOrFail()
                    ->translations,
                'fields.seo_title.en',
            ),
        );
        $this->assertSame(5, Service::query()
            ->whereNotNull('lead_form')
            ->get()
            ->filter(fn (Service $service): bool => data_get($service->lead_form, 'calculator_enabled') === true)
            ->count());
        $this->assertTrue(SeoPage::query()->where('key', 'service-calculator')->exists());
        $this->assertGreaterThanOrEqual(1, ProjectCategory::query()->count());
        $this->assertSame(0, Project::query()->whereNull('category_id')->count());
        $this->assertNotEmpty(data_get(Project::query()->first()?->translations, 'fields.title.en'));
        $translationEntries = data_get(
            SiteSetting::query()->where('key', 'translations')->value('value'),
            'entries',
        );

        $this->assertIsArray($translationEntries);
        $this->assertGreaterThan(300, count($translationEntries));
        $translationsByKey = collect($translationEntries)->keyBy('key');
        $blogNavigation = $translationsByKey->get('nav.blog');

        $this->assertSame('ბლოგი', $blogNavigation['ka'] ?? null);
        $this->assertSame('Blog', $blogNavigation['en'] ?? null);
        $this->assertSame('Блог', $blogNavigation['ru'] ?? null);

        foreach ($translationEntries as $entry) {
            $this->assertNotEmpty($entry['key'] ?? null);
            $this->assertNotEmpty($entry['ka'] ?? null, "Missing KA copy for {$entry['key']}");
            $this->assertNotEmpty($entry['en'] ?? null, "Missing EN copy for {$entry['key']}");
            $this->assertNotEmpty($entry['ru'] ?? null, "Missing RU copy for {$entry['key']}");
        }

        $this->assertSame(1, PrivacyPolicy::query()->count());
        $this->assertNotEmpty(data_get(
            PrivacyPolicy::query()->first()?->translations,
            'fields.content.ru',
        ));

        CategoryForService::query()->each(function (CategoryForService $category): void {
            $this->assertNotEmpty($category->seo_title);
            $this->assertNotEmpty($category->seo_description);
            $this->assertNotEmpty($category->intro_text);

            foreach (['ka', 'en', 'ru'] as $locale) {
                $this->assertNotEmpty(data_get($category->translations, "fields.seo_title.{$locale}"));
                $this->assertNotEmpty(data_get($category->translations, "fields.seo_description.{$locale}"));
                $this->assertNotEmpty(data_get($category->translations, "fields.intro_text.{$locale}"));
            }
        });
    }
}
