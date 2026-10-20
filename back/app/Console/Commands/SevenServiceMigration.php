<?php

namespace App\Console\Commands;

use Database\Seeders\GoogleBusinessServiceDefinitions;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Read-only preflight for the production seven-service consolidation.
 *
 * This command intentionally has no write mode until all legacy content
 * collisions, media, and historical references have a tested migration path.
 */
final class SevenServiceMigration extends Command
{
    protected $signature = 'safetech:seven-service-preflight\n        {--audit-collisions : Verify complete collision groups without changing any data}';

    protected $description = 'Audit service consolidation without modifying production data';

    public function handle(): int
    {
        $canonicalSlugs = ServiceCatalogSeeder::canonicalServiceSlugs();
        $aliases = GoogleBusinessServiceDefinitions::CANONICAL_ALIASES;

        if (count($canonicalSlugs) !== 7) {
            $this->error('Expected exactly seven canonical service definitions.');

            return self::FAILURE;
        }

        $services = DB::table('services')->get(['id', 'slug'])->keyBy('slug');
        $unknown = $services->keys()
            ->reject(fn (string $slug): bool => in_array($slug, $canonicalSlugs, true) || isset($aliases[$slug]))
            ->values();

        $this->info("Services: {$services->count()} (canonical: ".count($canonicalSlugs).')');
        $this->line("Unmapped legacy services: {$unknown->count()}");

        foreach ($unknown as $slug) {
            $this->error("Unmapped service: {$slug}");
        }

        foreach ($canonicalSlugs as $slug) {
            if (! $services->has($slug)) {
                $this->error("Missing canonical target: {$slug}");
            }
        }

        if ($unknown->isNotEmpty() || collect($canonicalSlugs)->contains(fn (string $slug): bool => ! $services->has($slug))) {
            return self::FAILURE;
        }

        $rows = [];
        $totalLandingCollisions = 0;
        $totalQuoteCollisions = 0;

        foreach ($services as $source) {
            if (in_array($source->slug, $canonicalSlugs, true)) {
                continue;
            }

            $targetSlug = $aliases[$source->slug];
            if (! in_array($targetSlug, $canonicalSlugs, true) || ! $services->has($targetSlug)) {
                $this->error("Invalid destination for {$source->slug}: {$targetSlug}");

                return self::FAILURE;
            }

            $targetId = $services->get($targetSlug)->id;
            $landingCollisions = 0;
            $linkedLandings = 0;
            $quotes = 0;
            $quoteCollisions = 0;

            if (Schema::hasTable('local_service_landings')) {
                $linkedLandings = DB::table('local_service_landings')->where('service_id', $source->id)->count();
                $landingCollisions = DB::table('local_service_landings as source')
                    ->join('local_service_landings as target', function ($join) use ($targetId): void {
                        $join->on('source.location_slug', '=', 'target.location_slug')
                            ->where('target.service_id', $targetId);
                    })
                    ->where('source.service_id', $source->id)
                    ->count();
            }

            if (Schema::hasTable('quote_catalog_items')) {
                $quotes = DB::table('quote_catalog_items')->where('service_id', $source->id)->count();
                $quoteCollisions = DB::table('quote_catalog_items as source')
                    ->join('quote_catalog_items as target', function ($join) use ($targetId): void {
                        $join->on('source.component_key', '=', 'target.component_key')
                            ->where('target.service_id', $targetId);
                    })
                    ->where('source.service_id', $source->id)
                    ->count();
            }

            $totalLandingCollisions += $landingCollisions;
            $totalQuoteCollisions += $quoteCollisions;
            $rows[] = [
                $source->slug,
                $targetSlug,
                $linkedLandings,
                $landingCollisions,
                $quotes,
                $quoteCollisions,
            ];
        }

        $this->table(
            ['Legacy', 'Canonical', 'SEO pages', 'Page collisions', 'Quote items', 'Quote collisions'],
            $rows,
        );
        $this->line("Initial collisions against canonical records: SEO {$totalLandingCollisions}, quotes {$totalQuoteCollisions}.");

        // Collision counts are indicative: two different legacy services can also
        // collide once both are moved to the same target.
        if ((bool) $this->option('audit-collisions')) {
            foreach ($canonicalSlugs as $targetSlug) {
                $sourceIds = $services
                    ->filter(fn (object $service): bool => $service->slug === $targetSlug
                        || ($aliases[$service->slug] ?? null) === $targetSlug)
                    ->pluck('id')
                    ->all();

                if ($sourceIds === []) {
                    continue;
                }

                foreach ([
                    ['local_service_landings', 'location_slug'],
                    ['quote_catalog_items', 'component_key'],
                ] as [$table, $key]) {
                    if (! Schema::hasTable($table)) {
                        continue;
                    }

                    $groups = DB::table($table)
                        ->select($key)
                        ->selectRaw('COUNT(*) AS duplicate_count')
                        ->whereIn('service_id', $sourceIds)
                        ->groupBy($key)
                        ->havingRaw('COUNT(*) > 1')
                        ->orderByDesc('duplicate_count')
                        ->get();

                    foreach ($groups as $group) {
                        $this->warn("FULL COLLISION {$targetSlug}: {$table}.{$key}={$group->{$key}} ({$group->duplicate_count} rows)");
                    }
                }
            }
        }

        $this->warn('READ-ONLY preflight completed. No records were updated or deleted.');
        $this->warn('A separate tested migration is required to reconcile content, project pivots and media.');

        return self::SUCCESS;
    }
}
