<?php

namespace Tests\Feature;

use App\Models\QuoteCatalogItem;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteCatalogPriceHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_and_supplier_changes_are_snapshotted(): void
    {
        $service = Service::factory()->create();
        $item = QuoteCatalogItem::query()->create([
            'service_id' => $service->id, 'component_key' => 'camera', 'name' => 'Camera',
            'supplier' => 'Supplier A', 'purchase_price' => 100, 'markup_percentage' => 50,
        ]);
        $item->update(['supplier' => 'Supplier B', 'purchase_price' => 120]);

        $this->assertCount(2, $item->priceHistory()->get());
        $this->assertDatabaseHas('quote_catalog_price_histories', [
            'quote_catalog_item_id' => $item->id,
            'supplier' => 'Supplier B',
            'purchase_price' => 120,
        ]);
    }
}
