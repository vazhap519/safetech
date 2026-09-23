<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\GoogleBusinessServicesSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleBusinessServicesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_the_google_business_catalog_without_deleting_existing_service_pages(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $original = Service::query()->where('slug', 'security-camera-installation')->firstOrFail();
        $originalId = $original->id;
        $originalSeoTitle = data_get($original->seo, 'title');

        $this->seed(GoogleBusinessServicesSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);

        $this->assertDatabaseCount('category_for_services', 5);
        // 47 GBP services and the original POS service, which is not deleted.
        $this->assertDatabaseCount('services', 48);

        $this->assertSame(47, Service::query()
            ->whereIn('slug', GoogleBusinessServicesSeeder::canonicalServiceSlugs())->count());

        $original->refresh();
        $this->assertSame($originalId, $original->id);
        $this->assertSame('ვიდეომეთვალყურეობის სისტემების მონტაჟი', $original->name);
        $this->assertSame($originalSeoTitle, data_get($original->seo, 'title'));
        $this->assertSame('CCTV System Installation', data_get($original->translations, 'fields.name.en'));
        $this->assertSame('Монтаж видеонаблюдения', data_get($original->translations, 'fields.card.title.ru'));
        $this->assertSame(3, $original->faqs()->count());

        $this->assertSame('IT მხარდაჭერა და მომსახურება',
            CategoryForService::query()->where('slug', 'business-it')->sole()->name);
        $this->assertSame('სუსტი დენები და საკომუნიკაციო ინფრასტრუქტურა',
            CategoryForService::query()->where('slug', 'telecommunications-contractor')->sole()->name);

        $this->assertDatabaseHas('services', ['slug' => 'pos-system-installation']);
        $this->assertDatabaseMissing('services', ['slug' => 'laptop-screen-repair']);

        $new = Service::query()->where('slug', 'mac-app-installation')->firstOrFail();
        $this->assertTrue($new->is_published);
        $this->assertTrue((bool) data_get($new->seo, 'noindex'));
        $this->assertSame('Mac-ზე პროგრამების ინსტალაცია', $new->name);
        $this->assertSame('Mac Software Installation', data_get($new->translations, 'fields.name.en'));
    }

    public function test_it_preserves_admin_overrides_and_updates_only_generated_public_names(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $service = Service::query()->where('slug', 'security-camera-installation')->firstOrFail();
        $service->title = 'რედაქტორის საკუთარი სათაური';
        $service->is_published = false;
        $service->save();

        $setting = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $value = $setting->value;
        $map = MultilingualContent::mapFrom($value);
        $map['service.security-camera-installation.name']['en'] = 'Custom English label';
        $value['entries'] = MultilingualContent::entriesFromMap($map);
        $setting->value = $value;
        $setting->save();

        $this->seed(GoogleBusinessServicesSeeder::class);

        $service->refresh();
        $setting->refresh();
        $map = MultilingualContent::mapFrom($setting->value);

        $this->assertSame('რედაქტორის საკუთარი სათაური', $service->title);
        $this->assertFalse($service->is_published);
        $this->assertSame('Custom English label',
            $map['service.security-camera-installation.name']['en']);
        $this->assertSame('ვიდეომეთვალყურეობის სისტემების მონტაჟი',
            $map['service.security-camera-installation.card.title']['ka']);
    }

    public function test_unindexed_catalog_cards_do_not_fail_strict_local_seo_coverage(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);

        $this->artisan('safetech:local-seo-audit', ['--strict' => true])
            ->expectsOutputToContain('12/12 indexable published services')
            ->assertExitCode(0);
    }

    public function test_deleted_google_catalog_services_stay_deleted_after_reseeding(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);

        Service::query()->where('slug', 'mac-app-installation')->firstOrFail()->delete();
        CategoryForService::query()->where('slug', 'telecommunications-contractor')->firstOrFail()->delete();

        $this->seed(GoogleBusinessServicesSeeder::class);

        $this->assertDatabaseMissing('services', ['slug' => 'mac-app-installation']);
        $this->assertDatabaseMissing('category_for_services',
            ['slug' => 'telecommunications-contractor']);
    }
}
