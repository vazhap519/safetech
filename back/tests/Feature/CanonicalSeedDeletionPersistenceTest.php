<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\Faq;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\ContentSeeder;
use Database\Seeders\SeoPageSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalSeedDeletionPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_canonical_records_are_not_recreated_by_later_seed_runs(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(SeoPageSeeder::class);

        $category = CategoryForService::query()
            ->where('slug', 'computer-services')
            ->firstOrFail();
        $service = Service::query()
            ->where('slug', 'business-it-support')
            ->firstOrFail();
        Faq::query()
            ->where('context', 'service:business-it-support:scope')
            ->firstOrFail();
        $contactFaq = Faq::query()
            ->whereNull('service_id')
            ->where('context', 'contact')
            ->where('sort_order', 100)
            ->firstOrFail();

        $contactFaq->delete();
        $service->delete();
        $category->delete();
        SiteSetting::query()->where('key', 'socials')->firstOrFail()->delete();
        SiteSetting::query()->where('key', 'translations')->firstOrFail()->delete();
        SeoPage::query()->where('key', 'contact')->firstOrFail()->delete();

        $this->seed(SystemContentSeeder::class);
        $this->seed(SeoPageSeeder::class);

        $this->assertDatabaseMissing('category_for_services', [
            'slug' => 'computer-services',
        ]);
        $this->assertDatabaseMissing('services', [
            'slug' => 'business-it-support',
        ]);
        $this->assertDatabaseMissing('faqs', [
            'context' => 'service:business-it-support:scope',
        ]);
        $this->assertDatabaseMissing('faqs', [
            'context' => 'contact',
            'sort_order' => 100,
        ]);
        $this->assertDatabaseMissing('site_settings', ['key' => 'socials']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'translations']);
        $this->assertDatabaseMissing('seo_pages', ['key' => 'contact']);

        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'service-category',
            'key' => 'computer-services',
        ]);
        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'service',
            'key' => 'business-it-support',
        ]);
        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'faq',
            'key' => 'service:business-it-support:scope',
        ]);
        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'faq',
            'key' => 'contact:100',
        ]);
        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'site-setting',
            'key' => 'socials',
        ]);
        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'seo-page',
            'key' => 'contact',
        ]);
    }

    public function test_explicit_empty_repeaters_survive_subsequent_seed_runs(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(SeoPageSeeder::class);

        $service = Service::query()
            ->where('slug', 'business-it-support')
            ->firstOrFail();
        $service->forceFill([
            'benefits' => [],
            'features' => [],
            'process' => [],
        ])->save();

        $category = CategoryForService::query()
            ->where('slug', 'computer-services')
            ->firstOrFail();
        $category->faq = [];
        $category->save();

        $socials = SiteSetting::query()->where('key', 'socials')->firstOrFail();
        $socialsValue = $socials->value;
        $socialsValue['links'] = [];
        $socialsValue['share_buttons'] = [];
        $socials->value = $socialsValue;
        $socials->save();

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $translations->value = ['entries' => []];
        $translations->save();

        $seo = SeoPage::query()->where('key', 'contact')->firstOrFail();
        $seo->keywords = [];
        $seo->translations = [];
        $seo->save();

        $this->seed(ContentSeeder::class);
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(SeoPageSeeder::class);

        $service->refresh();
        $category->refresh();
        $socials->refresh();
        $translations->refresh();
        $seo->refresh();

        $this->assertSame([], $service->benefits);
        $this->assertSame([], $service->features);
        $this->assertSame([], $service->process);
        $this->assertSame([], $category->faq);
        $this->assertSame([], data_get($socials->value, 'links'));
        $this->assertSame([], data_get($socials->value, 'share_buttons'));
        $this->assertSame([], data_get($translations->value, 'entries'));
        $this->assertSame([], $seo->keywords);
        $this->assertSame([], $seo->translations);
    }

    public function test_deleted_canonical_translation_entries_are_not_reseeded_and_can_be_restored(): void
    {
        $this->seed(SystemContentSeeder::class);

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $value = $translations->value;
        $map = MultilingualContent::mapFrom($value);

        foreach ([
            'nav.home',
            'consultation.form.submit',
            'privacy.title',
            'service.business-it-support.title',
        ] as $key) {
            unset($map[$key]);
        }

        $value['entries'] = MultilingualContent::entriesFromMap($map);
        $translations->value = $value;
        $translations->save();

        foreach ([
            'nav.home',
            'consultation.form.submit',
            'privacy.title',
            'service.business-it-support.title',
        ] as $key) {
            $this->assertDatabaseHas('seed_deletion_tombstones', [
                'type' => 'translation-entry',
                'key' => $key,
            ]);
        }

        $this->seed(SystemContentSeeder::class);

        $translations->refresh();
        $map = MultilingualContent::mapFrom($translations->value);

        $this->assertArrayNotHasKey('nav.home', $map);
        $this->assertArrayNotHasKey('consultation.form.submit', $map);
        $this->assertArrayNotHasKey('privacy.title', $map);
        $this->assertArrayNotHasKey('service.business-it-support.title', $map);

        $map['nav.home'] = [
            'ka' => 'ჩემი მთავარი',
            'en' => 'My home',
            'ru' => 'Моя главная',
        ];
        $value = $translations->value;
        $value['entries'] = MultilingualContent::entriesFromMap($map);
        $translations->value = $value;
        $translations->save();

        $this->assertDatabaseMissing('seed_deletion_tombstones', [
            'type' => 'translation-entry',
            'key' => 'nav.home',
        ]);

        $this->seed(SystemContentSeeder::class);

        $translations->refresh();
        $map = MultilingualContent::mapFrom($translations->value);

        $this->assertSame('ჩემი მთავარი', $map['nav.home']['ka']);
        $this->assertSame('My home', $map['nav.home']['en']);
        $this->assertSame('Моя главная', $map['nav.home']['ru']);
    }
}
