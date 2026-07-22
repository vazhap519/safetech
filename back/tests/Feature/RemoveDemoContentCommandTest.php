<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\PrivacyPolicy;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveDemoContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_only_bundled_demo_catalog_records(): void
    {
        config()->set('app.seed_demo_content', true);
        $this->seed(ContentSeeder::class);

        $customService = Service::query()->where('slug', 'cctv')->firstOrFail()->replicate();
        $customService->forceFill([
            'slug' => 'custom-security-service',
            'name' => 'Custom security service',
            'title' => 'Custom security service',
        ])->save();

        $customProject = Project::query()->where('slug', 'office-network-upgrade')->firstOrFail()->replicate();
        $customProject->forceFill([
            'slug' => 'custom-office-project',
            'name' => 'Custom office project',
            'title' => 'Custom office project',
        ])->save();

        $translationSetting = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $translationValue = $translationSetting->value;
        $editedEntryIndex = collect($translationValue['entries'])->search(
            fn (array $entry): bool => ($entry['key'] ?? null) === 'home.hero.titlePrefix',
        );
        $this->assertNotFalse($editedEntryIndex);
        $translationValue['entries'][$editedEntryIndex]['en'] = 'Administrator homepage title';
        $translationSetting->forceFill(['value' => $translationValue])->save();

        $this->artisan('cms:remove-demo-content', ['--force' => true])
            ->assertSuccessful();

        foreach (['cctv', 'networking', 'access-control', 'server-infrastructure', 'it-support'] as $slug) {
            $this->assertDatabaseMissing('services', ['slug' => $slug]);
        }

        $this->assertDatabaseMissing('projects', ['slug' => 'office-network-upgrade']);
        $this->assertDatabaseHas('services', ['slug' => 'custom-security-service']);
        $this->assertDatabaseHas('projects', ['slug' => 'custom-office-project']);
        $this->assertDatabaseHas('category_for_services', ['slug' => 'security-systems']);
        $this->assertDatabaseMissing('category_for_services', ['slug' => 'network-infrastructure']);
        $this->assertDatabaseMissing('category_for_services', ['slug' => 'server-infrastructure']);
        $this->assertDatabaseMissing('category_for_services', ['slug' => 'it-support']);
        $this->assertDatabaseHas('project_categories', ['slug' => 'offices']);
        $this->assertDatabaseHas('site_settings', ['key' => 'translations']);

        $translations = collect(
            SiteSetting::query()->where('key', 'translations')->value('value')['entries'] ?? [],
        )->keyBy('key');

        $this->assertNotEmpty($translations->get('nav.home'));
        $this->assertSame(
            'Administrator homepage title',
            $translations->get('home.hero.titlePrefix')['en'] ?? null,
        );
        $this->assertNull($translations->get('home.infrastructure.title'));
        $this->assertNull($translations->get('about.hero.title'));
    }

    public function test_content_seeder_keeps_system_defaults_but_skips_demo_catalog_in_production(): void
    {
        config()->set('app.seed_demo_content', false);

        $this->seed(ContentSeeder::class);

        $this->assertSame(0, Service::query()->count());
        $this->assertSame(0, Project::query()->count());
        $this->assertSame(0, CategoryForService::query()->count());
        $this->assertSame(0, ProjectCategory::query()->count());
        $this->assertTrue(SiteSetting::query()->where('key', 'translations')->exists());
        $this->assertSame(1, PrivacyPolicy::query()->count());
    }

    public function test_system_and_demo_seeders_have_isolated_responsibilities(): void
    {
        config()->set('app.seed_demo_content', true);

        $this->seed(SystemContentSeeder::class);

        $this->assertTrue(SiteSetting::query()->where('key', 'translations')->exists());
        $systemTranslations = collect(
            SiteSetting::query()->where('key', 'translations')->value('value')['entries'] ?? [],
        )->keyBy('key');
        $this->assertNotEmpty($systemTranslations->get('nav.home'));
        $this->assertNull($systemTranslations->get('home.hero.titlePrefix'));
        $this->assertNull($systemTranslations->get('services.hero.titlePrefix'));
        $this->assertSame(1, PrivacyPolicy::query()->count());
        $this->assertSame(0, Service::query()->count());
        $this->assertSame(0, Project::query()->count());

        $this->seed(DemoContentSeeder::class);

        $this->assertGreaterThan(0, Service::query()->count());
        $this->assertGreaterThan(0, Project::query()->count());
        $demoTranslations = collect(
            SiteSetting::query()->where('key', 'translations')->value('value')['entries'] ?? [],
        )->keyBy('key');
        $this->assertNotEmpty($demoTranslations->get('home.hero.titlePrefix'));
        $this->assertNotEmpty($demoTranslations->get('services.hero.titlePrefix'));
    }

    public function test_demo_cleanup_preserves_edited_records_with_bundled_slugs(): void
    {
        config()->set('app.seed_demo_content', true);
        $this->seed(ContentSeeder::class);

        Service::query()->where('slug', 'cctv')->firstOrFail()->update([
            'title' => 'Custom CCTV service',
        ]);
        Project::query()->where('slug', 'office-network-upgrade')->firstOrFail()->update([
            'description' => 'A real customer project managed from the CMS.',
        ]);

        $this->artisan('cms:remove-demo-content', ['--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('services', [
            'slug' => 'cctv',
            'title' => 'Custom CCTV service',
        ]);
        $this->assertDatabaseHas('projects', [
            'slug' => 'office-network-upgrade',
            'description' => 'A real customer project managed from the CMS.',
        ]);
        $this->assertDatabaseMissing('services', ['slug' => 'networking']);
    }
}
