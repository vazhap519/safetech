<?php

use App\Models\Faq;
use App\Support\Calculators\CanonicalServiceCalculator;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $canonicalSlugs = CanonicalServiceCalculator::slugs();
        $canonicalContexts = ServiceCatalogSeeder::canonicalFaqContexts();

        Faq::query()
            ->where(function ($legacy) use ($canonicalSlugs, $canonicalContexts): void {
                $legacy
                    ->whereHas('service', fn ($service) => $service->whereNotIn('slug', $canonicalSlugs))
                    ->orWhere(function ($orphaned) use ($canonicalContexts): void {
                        $orphaned
                            ->whereNull('service_id')
                            ->where('context', 'like', 'service:%')
                            ->whereNotIn('context', $canonicalContexts);
                    });
            })
            ->get()
            ->each(static fn (Faq $faq): bool => $faq->delete());
    }

    public function down(): void
    {
        // Removed FAQs belonged to services that are no longer offered.
    }
};
