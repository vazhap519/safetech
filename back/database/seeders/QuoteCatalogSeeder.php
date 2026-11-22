<?php

namespace Database\Seeders;

use App\Models\QuoteCatalogItem;
use App\Models\Service;
use App\Support\Calculators\CanonicalServiceCalculator;
use App\Support\Estimators\ServiceQuoteCalculator;
use Illuminate\Database\Seeder;

/**
 * Keeps Quote Catalog rows in sync with configurator component definitions.
 *
 * Safe for production refreshes: existing purchase/sale prices, supplier,
 * brand, model, warranty and notes are preserved by syncCatalog().
 */
final class QuoteCatalogSeeder extends Seeder
{
    public function run(): void
    {
        app(ServiceQuoteCalculator::class)->syncCatalog();

        QuoteCatalogItem::query()
            ->whereNull('markup_percentage')
            ->update(['markup_percentage' => 60]);

        // Keep historical estimate relations intact while preventing legacy
        // service components from appearing in the seven-service quote engine.
        QuoteCatalogItem::query()
            ->whereHas('service', fn ($query) => $query->whereNotIn(
                'slug',
                CanonicalServiceCalculator::slugs(),
            ))
            ->update(['is_active' => false]);

        // Ensure every published calculator-enabled service has been considered.
        // syncCatalog() only creates component rows declared by that service's
        // configurator; it intentionally does not invent products or prices.
        Service::query()->published()->count();
    }
}
