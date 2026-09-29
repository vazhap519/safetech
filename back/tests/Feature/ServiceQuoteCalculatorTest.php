<?php

namespace Tests\Feature;

use App\Models\QuoteCatalogItem;
use App\Models\Service;
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

        $this->assertSame('200.00', $item->sale_price);

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
}
