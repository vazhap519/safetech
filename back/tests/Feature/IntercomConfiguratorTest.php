<?php

namespace Tests\Feature;

use App\Filament\Pages\AccessIntercomConfiguratorPage;
use App\Models\Service;
use App\Models\User;
use App\Support\Calculators\DefaultCalculatorProfiles;
use App\Support\Calculators\DefaultConfiguratorComponents;
use Database\Seeders\IntercomConfiguratorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IntercomConfiguratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_upgrades_existing_intercom_without_losing_prices_and_is_idempotent(): void
    {
        $service = Service::query()->create([
            'slug' => 'intercom-access-control-installation', 'name' => 'დომოფონი', 'title' => 'დომოფონი', 'description' => 'IP დომოფონი', 'seo_description' => 'IP დომოფონი', 'is_published' => true,
            'lead_form' => ['pricing' => ['labor_price' => 777], 'extra_fields' => [['key' => 'doors', 'type' => 'number', 'max' => 200]], 'components' => [['key' => 'tvt-td-e2137', 'unit_price' => 321]]],
        ]);
        $this->seed(IntercomConfiguratorSeeder::class);
        $config = $service->fresh()->lead_form;
        $fields = array_column($config['extra_fields'], null, 'key');
        $this->assertSame(array_map('strval', range(1, 100)), array_column($fields['apartments']['options'], 'value'));
        $this->assertSame(array_map('strval', range(1, 10)), array_column($fields['doors']['options'], 'value'));
        $this->assertSame(777, $config['pricing']['labor_price']);
        $this->assertSame(321, array_column($config['components'], 'unit_price', 'key')['tvt-td-e2137']);
        $this->seed(IntercomConfiguratorSeeder::class);
        $this->assertSame($config, $service->fresh()->lead_form);
        foreach (['ka', 'en', 'ru'] as $locale) {
            $response = $this->getJson('/api/service-calculator/profiles?locale='.$locale.'&service='.$service->slug)->assertOk();
            $profile = $response->json('data.0');
            $this->assertCount(100, $profile['fields'][0]['options']);
            $this->assertCount(10, $profile['fields'][1]['options']);
            $this->assertSame(500, $profile['intercomCatalog']['doorStations']['tvt-td-e2223']['max_apartments']);
            $components = array_column($profile['components'], null, 'key');
            $this->assertTrue($components['lock-psu']['priceOnRequest']);
            $this->assertFalse($components['tvt-td-e2137']['priceOnRequest']);
            $this->assertTrue($components['tvt-td-e2137']['quantityLocked']);
        }
    }

    public function test_video_intercom_names_are_not_classified_as_cctv(): void
    {
        $profile = DefaultCalculatorProfiles::for('video-intercom', 'ვიდეოდომოფონი');
        $this->assertSame('apartments', $profile['extra_fields'][0]['key']);
        $this->assertSame('tvt-td-e2223', DefaultConfiguratorComponents::for('video-intercom')[0]['key']);
        $this->assertSame('camera_technology', DefaultCalculatorProfiles::for('cctv')['extra_fields'][0]['key']);
    }

    public function test_admin_requires_scope_then_recalculates_the_full_kit(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Livewire::test(AccessIntercomConfiguratorPage::class)
            ->assertSet('scopeConfirmed', false)
            ->set('config.apartments', '100')
            ->set('config.doors', '10')
            ->call('showDevices')
            ->assertSet('scopeConfirmed', true)
            ->assertSee('100 ბინა/აბონენტი')
            ->assertSee('TD-E2223')
            ->assertSee('სრული კომპლექტაცია')
            ->set('config.doors', '2')
            ->assertSet('scopeConfirmed', false);
    }
}
