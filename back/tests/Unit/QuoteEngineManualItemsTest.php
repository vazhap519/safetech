<?php

namespace Tests\Unit;

use App\Filament\Pages\QuoteEnginePage;
use Tests\TestCase;

class QuoteEngineManualItemsTest extends TestCase
{
    public function test_manual_component_sale_price_is_derived_from_cost_markup_and_quantity(): void
    {
        $page = new QuoteEnginePage;
        $page->manualItems = [[
            'category' => 'equipment',
            'label' => 'IP camera',
            'quantity' => 3,
            'unit' => 'pcs',
            'purchase_price' => 100,
            'markup_percentage' => 25,
        ]];

        $item = $page->normalizedManualItems()[0];

        $this->assertSame(125.0, $item['sale_price']);
        $this->assertSame(300.0, $item['cost_total']);
        $this->assertSame(375.0, $item['sale_total']);
        $this->assertSame([
            'cost' => 300.0,
            'sale' => 375.0,
            'profit' => 75.0,
        ], $page->manualTotals());
    }
}
