<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\PrivacyPolicy;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_default_sync_does_not_create_demo_content(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config()->set('app.seed_demo_content', true);

        try {
            (new ContentSeeder)->run();

            $this->assertSame(0, Service::query()->count());
            $this->assertSame(0, Project::query()->count());
            $translations = collect(
                SiteSetting::query()->where('key', 'translations')->value('value')['entries'] ?? [],
            )->keyBy('key');

            $this->assertNotEmpty($translations->get('nav.home'));
            $this->assertNotEmpty($translations->get('forms.email'));
            $this->assertNull($translations->get('home.hero.titlePrefix'));
            $this->assertNull($translations->get('about.hero.title'));
            $this->assertNull($translations->get('meta.home.title'));
        } finally {
            $this->app->detectEnvironment(fn (): string => 'testing');
        }
    }

    public function test_google_analytics_default_is_installed_without_overwriting_admin_changes(): void
    {
        SiteSetting::query()->where('key', 'integrations')->delete();
        $this->app->detectEnvironment(fn (): string => 'production');

        try {
            (new ContentSeeder)->run();

            $integrations = SiteSetting::query()
                ->where('key', 'integrations')
                ->firstOrFail();

            $this->assertTrue((bool) data_get($integrations->value, 'marketing_enabled'));
            $this->assertSame(
                'G-VC9XHNPEG5',
                data_get($integrations->value, 'google_analytics_id'),
            );

            $adminValue = $integrations->value;
            $adminValue['marketing_enabled'] = false;
            $adminValue['google_analytics_id'] = 'G-ADMIN12345';
            $integrations->forceFill(['value' => $adminValue])->save();

            (new ContentSeeder)->run();

            $integrations->refresh();
            $this->assertFalse((bool) data_get($integrations->value, 'marketing_enabled'));
            $this->assertSame(
                'G-ADMIN12345',
                data_get($integrations->value, 'google_analytics_id'),
            );
        } finally {
            $this->app->detectEnvironment(fn (): string => 'testing');
        }
    }

    public function test_google_analytics_migration_preserves_existing_admin_configuration(): void
    {
        $integrations = SiteSetting::query()
            ->where('key', 'integrations')
            ->firstOrFail();
        $adminValue = $integrations->value;
        $adminValue['marketing_enabled'] = false;
        $adminValue['google_analytics_id'] = 'G-ADMIN12345';
        $integrations->forceFill(['value' => $adminValue])->save();

        $migration = require database_path(
            'migrations/2026_07_22_000001_configure_google_analytics.php',
        );
        $migration->up();

        $integrations->refresh();
        $this->assertFalse((bool) data_get($integrations->value, 'marketing_enabled'));
        $this->assertSame(
            'G-ADMIN12345',
            data_get($integrations->value, 'google_analytics_id'),
        );
    }

    public function test_default_sync_repairs_missing_privacy_translations_without_overwriting_admin_copy(): void
    {
        PrivacyPolicy::query()->create([
            'title' => 'Administrator privacy title',
            'highlight' => 'Administrator privacy highlight',
            'content' => '<p>Administrator privacy content</p>',
            'translations' => [
                'fields' => [
                    'title' => [
                        'ka' => 'Administrator KA title',
                        'en' => '',
                    ],
                    'content' => [
                        'ka' => '<p>Administrator KA content</p>',
                        'ru' => '',
                    ],
                ],
            ],
        ]);
        $this->app->detectEnvironment(fn (): string => 'production');

        try {
            (new ContentSeeder)->run();
        } finally {
            $this->app->detectEnvironment(fn (): string => 'testing');
        }

        $privacy = PrivacyPolicy::query()->firstOrFail();

        $this->assertSame('Administrator privacy title', $privacy->title);
        $this->assertSame(
            'Administrator KA title',
            data_get($privacy->translations, 'fields.title.ka'),
        );
        $this->assertSame(
            '<p>Administrator KA content</p>',
            data_get($privacy->translations, 'fields.content.ka'),
        );
        $this->assertSame('Privacy Policy', data_get($privacy->translations, 'fields.title.en'));
        $this->assertNotEmpty(data_get($privacy->translations, 'fields.title.ru'));
        $this->assertNotEmpty(data_get($privacy->translations, 'fields.content.en'));
        $this->assertNotEmpty(data_get($privacy->translations, 'fields.content.ru'));
        $this->assertSame(1, PrivacyPolicy::query()->count());
    }

    public function test_privacy_policy_changes_clear_every_localized_api_cache(): void
    {
        foreach (['ka', 'en', 'ru'] as $locale) {
            Cache::put("privacy_page:{$locale}", ['title' => 'Stale'], 300);
        }

        PrivacyPolicy::query()->create([
            'title' => 'Privacy',
            'highlight' => 'Privacy highlight',
            'content' => '<p>Privacy content</p>',
        ]);

        foreach (['ka', 'en', 'ru'] as $locale) {
            $this->assertFalse(Cache::has("privacy_page:{$locale}"));
        }
    }

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

        $translationSetting = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $translationValue = $translationSetting->value;
        $aboutHeroIndex = collect($translationValue['entries'])->search(
            fn (array $entry): bool => ($entry['key'] ?? null) === 'about.hero.title',
        );
        $translationValue['entries'][$aboutHeroIndex]['en'] = '';
        $translationValue['entries'][$aboutHeroIndex]['ru'] = 'Администраторский заголовок';
        $translationSetting->forceFill(['value' => $translationValue])->save();

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
        $aboutHero = $translationsByKey->get('about.hero.title');

        $this->assertSame('ბლოგი', $blogNavigation['ka'] ?? null);
        $this->assertSame('Blog', $blogNavigation['en'] ?? null);
        $this->assertSame('Блог', $blogNavigation['ru'] ?? null);
        $this->assertSame('About SafeTech', $aboutHero['en'] ?? null);
        $this->assertSame('Администраторский заголовок', $aboutHero['ru'] ?? null);

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
