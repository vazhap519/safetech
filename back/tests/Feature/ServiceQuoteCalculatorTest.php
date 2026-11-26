<?php

namespace Tests\Feature;

use App\Models\QuoteCatalogItem;
use App\Models\Service;
use App\Support\Calculators\CalculatorProfileBuilder;
use App\Support\Calculators\CanonicalServiceCalculator;
use App\Support\Estimators\ServiceQuoteCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceQuoteCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_a_service_quote_from_catalog_costs_markup_labor_and_discount(): void
    {
        $service = Service::query()->create([
            'slug' => 'quote-test-service',
            'name' => 'ტესტ სერვისი',
            'title' => 'ტესტ სერვისი',
            'description' => 'ტესტი',
            'seo_description' => 'ტესტი',
            'is_published' => true,
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => [
                    'currency' => 'GEL',
                    'base_price' => 100,
                    'labor_price' => 200,
                ],
                'project_size_options' => [],
                'property_type_options' => [],
                'packages' => [],
                'extra_fields' => [
                    [
                        'key' => 'device_count',
                        'type' => 'number',
                        'ka' => 'მოწყობილობების რაოდენობა',
                        'default' => 4,
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'components' => [
                    [
                        'key' => 'device',
                        'category' => 'camera',
                        'title_ka' => 'მოწყობილობა',
                        'unit_price' => 0,
                        'quantity_mode' => 'field',
                        'quantity_field' => 'device_count',
                        'required' => true,
                        'recommended' => true,
                        'rules' => [],
                    ],
                ],
            ],
        ]);

        QuoteCatalogItem::query()->create([
            'service_id' => $service->getKey(),
            'component_key' => 'device',
            'brand' => 'SafeCam',
            'model' => 'SC-4MP',
            'name' => 'მოწყობილობა',
            'category' => 'camera',
            'purchase_price' => 100,
            'markup_percentage' => 60,
            'sale_price' => null,
            'is_active' => true,
        ]);

        $quote = app(ServiceQuoteCalculator::class)->calculate($service, [
            'values' => ['device_count' => 4],
            'labor_price' => 200,
            'discount_percentage' => 10,
        ]);

        $this->assertSame(100.0, $quote['service_subtotal']);
        $this->assertSame(200.0, $quote['labor_subtotal']);
        $this->assertSame(640.0, $quote['component_subtotal']);
        $this->assertSame(94.0, $quote['discount_amount']);
        $this->assertSame(846.0, $quote['final_total']);
        $this->assertSame(400.0, $quote['known_cost_total']);
        $this->assertSame(446.0, $quote['profit_total']);
        $this->assertSame(52.72, $quote['gross_margin_percentage']);
        $this->assertTrue($quote['pricing_complete']);
        $this->assertSame(160.0, $quote['components'][0]['sale_price']);
        $this->assertSame(4.0, $quote['components'][0]['quantity']);

        $publicProfile = app(CalculatorProfileBuilder::class)->build($service, 'ka');
        $this->assertSame('SafeCam', $publicProfile['components'][0]['brand']);
        $this->assertSame('SC-4MP', $publicProfile['components'][0]['model']);
        $this->assertSame(160.0, $publicProfile['components'][0]['unitPrice']);
        $this->assertFalse($publicProfile['components'][0]['priceOnRequest']);
    }

    public function test_it_marks_profit_incomplete_when_equipment_purchase_cost_is_missing(): void
    {
        $service = Service::query()->create([
            'slug' => 'quote-missing-cost',
            'name' => 'ტესტი',
            'title' => 'ტესტი',
            'description' => 'ტესტი',
            'seo_description' => 'ტესტი',
            'is_published' => true,
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => ['currency' => 'GEL'],
                'project_size_options' => [],
                'property_type_options' => [],
                'packages' => [],
                'extra_fields' => [],
                'components' => [
                    [
                        'key' => 'equipment',
                        'category' => 'network',
                        'title_ka' => 'ქსელის მოწყობილობა',
                        'unit_price' => 250,
                        'quantity_mode' => 'fixed',
                        'default_quantity' => 1,
                        'required' => true,
                        'recommended' => true,
                        'rules' => [],
                    ],
                ],
            ],
        ]);

        $quote = app(ServiceQuoteCalculator::class)->calculate($service);

        $this->assertSame(250.0, $quote['final_total']);
        $this->assertFalse($quote['pricing_complete']);
        $this->assertSame(1, $quote['missing_cost_count']);
        $this->assertNull($quote['profit_total']);
        $this->assertNull($quote['gross_margin_percentage']);
    }

    public function test_barrier_quote_uses_specialized_lpr_profile_and_normalizes_incompatible_model(): void
    {
        $service = Service::query()->create([
            'slug' => 'barrier-gate-installation',
            'name' => 'შლაგბაუმების მონტაჟი',
            'title' => 'შლაგბაუმების მონტაჟი',
            'description' => 'შლაგბაუმის მონტაჟი',
            'seo_description' => 'შლაგბაუმის მონტაჟი',
            'is_published' => true,
            'lead_form' => [],
        ]);

        $quote = app(ServiceQuoteCalculator::class)->calculate($service, [
            'values' => [
                'lanes' => 2,
                'boom_length' => 4.5,
                'boom_type' => 'straight',
                'barrier_id' => 'hikvision-tmg4b0-3m',
                'access_mode' => 'lpr',
                'lpr_camera_id' => 'hikvision-tcg406-e',
                'vehicle_trigger' => 'video',
            ],
        ]);

        $this->assertNotSame('hikvision-tmg4b0-3m', $quote['values']['barrier_id']);

        $components = collect($quote['components'])->keyBy('key');

        $this->assertSame(2.0, $components[$quote['values']['barrier_id']]['quantity']);
        $this->assertSame(2.0, $components['hikvision-tcg406-e']['quantity']);
        $this->assertSame(2.0, $components['loop-detector']['quantity']);
        $this->assertSame(2.0, $components['safety-photocell']['quantity']);
        $this->assertSame(1.0, $components['lpr-network']['quantity']);
        $this->assertArrayHasKey('barrier-ups', $components->all());
        $this->assertArrayHasKey('barrier-installation', $components->all());
    }

    public function test_catalog_sync_creates_missing_component_rows_without_overwriting_prices(): void
    {
        $service = Service::query()->create([
            'slug' => 'quote-sync',
            'name' => 'ტესტი',
            'title' => 'ტესტი',
            'description' => 'ტესტი',
            'seo_description' => 'ტესტი',
            'is_published' => true,
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => ['currency' => 'GEL'],
                'project_size_options' => [],
                'property_type_options' => [],
                'packages' => [],
                'extra_fields' => [],
                'components' => [
                    [
                        'key' => 'switch-8',
                        'category' => 'network',
                        'title_ka' => '8-პორტიანი სვიჩი',
                        'unit_price' => 200,
                        'quantity_mode' => 'fixed',
                        'default_quantity' => 1,
                        'required' => true,
                        'recommended' => true,
                        'rules' => [],
                    ],
                ],
            ],
        ]);

        $calculator = app(ServiceQuoteCalculator::class);

        $this->assertSame(1, $calculator->syncCatalog($service->getKey()));

        $item = QuoteCatalogItem::query()
            ->where('service_id', $service->getKey())
            ->where('component_key', 'switch-8')
            ->firstOrFail();

        $this->assertNull($item->sale_price);

        $fallbackQuote = $calculator->calculate($service);
        $this->assertSame(200.0, $fallbackQuote['components'][0]['sale_price']);

        $item->update([
            'purchase_price' => 90,
            'sale_price' => 175,
            'supplier' => 'Supplier A',
        ]);

        $this->assertSame(0, $calculator->syncCatalog($service->getKey()));

        $item->refresh();
        $this->assertSame('90.00', $item->purchase_price);
        $this->assertSame('175.00', $item->sale_price);
        $this->assertSame('Supplier A', $item->supplier);
    }

    public function test_cctv_quote_derives_storage_and_8tb_disk_count_from_recording_profile(): void
    {
        $service = Service::query()->create([
            'slug' => 'security-camera-installation', 'name' => 'CCTV', 'title' => 'CCTV',
            'description' => 'CCTV', 'seo_description' => 'CCTV', 'is_published' => true,
            'lead_form' => CanonicalServiceCalculator::for('security-camera-installation', 'CCTV'),
        ]);

        $quote = app(ServiceQuoteCalculator::class)->calculate($service, ['values' => [
            'camera_count' => 32, 'recording_days' => 30, 'resolution' => '4mp',
            'recording_fps' => 15, 'recording_codec' => 'h265', 'recording_mode' => 'continuous',
            'storage_reserve_percent' => 20,
        ]]);
        $storage = collect($quote['components'])->firstWhere('key', 'storage');

        $this->assertGreaterThan(0, $quote['values']['required_storage_tb']);
        $this->assertSame(
            (float) ceil($quote['values']['required_storage_tb'] / 8),
            $storage['quantity'],
        );
    }

    public function test_cctv_quote_only_returns_the_matching_camera_and_nvr_class(): void
    {
        $service = Service::query()->create([
            'slug' => 'security-camera-installation', 'name' => 'CCTV', 'title' => 'CCTV',
            'description' => 'CCTV', 'seo_description' => 'CCTV', 'is_published' => true,
            'lead_form' => CanonicalServiceCalculator::for('security-camera-installation', 'CCTV'),
        ]);

        $quote = app(ServiceQuoteCalculator::class)->calculate($service, ['values' => [
            'camera_technology' => 'ip', 'camera_count' => 12, 'resolution' => '4mp',
        ]]);
        $keys = collect($quote['components'])->pluck('key');

        $this->assertTrue($keys->contains('ip-camera-4mp'));
        $this->assertTrue($keys->contains('nvr-16ch-160mbps'));
        $this->assertTrue($keys->contains('poe-switch-16'));
        $this->assertFalse($keys->contains('ip-camera-2mp'));
        $this->assertFalse($keys->contains('nvr-8ch-80mbps'));
    }

    public function test_real_nvr_catalog_limits_are_applied_to_public_profile_and_quote_engine(): void
    {
        $service = Service::query()->create([
            'slug' => 'security-camera-installation', 'name' => 'CCTV', 'title' => 'CCTV',
            'description' => 'CCTV', 'seo_description' => 'CCTV', 'is_published' => true,
            'lead_form' => CanonicalServiceCalculator::for('security-camera-installation', 'CCTV'),
        ]);
        $catalog = QuoteCatalogItem::query()->create([
            'service_id' => $service->id,
            'component_key' => 'nvr-16ch-160mbps',
            'name' => 'Professional 16-channel NVR',
            'category' => 'recorder',
            'brand' => 'SafeVision',
            'model' => 'SV-NVR16',
            'technical_specs' => [
                'channels' => 16,
                'incoming_bandwidth_mbps' => 160,
                'max_resolution_mp' => 8,
                'sata_bays' => 2,
                'max_hdd_tb' => 10,
                'raid_levels' => ['none', 'raid1'],
                'codecs' => ['h265'],
                'onvif' => true,
            ],
            'is_active' => true,
        ]);

        $profile = app(CalculatorProfileBuilder::class)->build($service, 'en');
        $recorder = collect($profile['components'])->firstWhere('key', 'nvr-16ch-160mbps');

        $this->assertSame('SV-NVR16', $recorder['model']);
        $this->assertSame(160, $recorder['technicalSpecs']['incoming_bandwidth_mbps']);
        $this->assertContains(
            ['field' => 'resolution_mp', 'operator' => 'lte', 'value' => '8'],
            $recorder['rules'],
        );

        $compatibleQuote = app(ServiceQuoteCalculator::class)->calculate($service, ['values' => [
            'camera_technology' => 'ip', 'camera_count' => 12, 'resolution' => '4mp',
            'recording_days' => 14, 'recording_fps' => 15, 'recording_codec' => 'h265', 'storage_raid' => 'none',
        ]]);
        $this->assertContains('nvr-16ch-160mbps', collect($compatibleQuote['components'])->pluck('key'));

        $catalog->update(['technical_specs' => array_merge(
            $catalog->technical_specs,
            ['max_resolution_mp' => 2],
        )]);
        $incompatibleQuote = app(ServiceQuoteCalculator::class)->calculate($service, ['values' => [
            'camera_technology' => 'ip', 'camera_count' => 12, 'resolution' => '4mp',
            'recording_days' => 14, 'recording_fps' => 15, 'recording_codec' => 'h265', 'storage_raid' => 'none',
        ]]);
        $this->assertNotContains('nvr-16ch-160mbps', collect($incompatibleQuote['components'])->pluck('key'));
    }

    public function test_low_voltage_quote_estimates_cable_from_area_and_points(): void
    {
        $service = Service::query()->create([
            'slug' => 'low-voltage-cabling', 'name' => 'Low voltage', 'title' => 'Low voltage',
            'description' => 'Low voltage', 'seo_description' => 'Low voltage', 'is_published' => true,
            'lead_form' => CanonicalServiceCalculator::for('low-voltage-cabling', 'Low voltage'),
        ]);

        $quote = app(ServiceQuoteCalculator::class)->calculate($service, ['values' => [
            'area_sqm' => 400, 'network_points' => 20, 'cable_estimation_mode' => 'auto',
        ]]);
        $cable = collect($quote['components'])->firstWhere('key', 'low-voltage-cat6');

        $this->assertSame(30.0, $quote['values']['estimated_average_run_m']);
        $this->assertSame(672, $quote['values']['estimated_cable_meters']);
        $this->assertSame(672.0, $cable['quantity']);
    }

    public function test_canonical_runtime_upgrades_legacy_saved_component_definitions(): void
    {
        $service = new Service([
            'slug' => 'security-camera-installation',
            'name' => 'CCTV',
            'lead_form' => [
                'pricing' => ['labor_price' => 321],
                'components' => [[
                    'key' => 'recorder',
                    'title_en' => 'Legacy generic recorder',
                    'quantity_mode' => 'ceil',
                    'quantity_field' => 'camera_count',
                ]],
            ],
        ]);

        $config = app(CalculatorProfileBuilder::class)->config($service);
        $keys = collect($config['components'])->pluck('key');

        $this->assertSame(321, $config['pricing']['labor_price']);
        $this->assertTrue($keys->contains('nvr-16ch-160mbps'));
        $this->assertFalse($keys->contains('recorder'));
    }
}
