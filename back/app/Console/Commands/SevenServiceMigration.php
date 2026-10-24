<?php

namespace App\Console\Commands;

use Database\Seeders\GoogleBusinessServiceDefinitions;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Audit and transactionally consolidate public services while retaining historical records.
 */
final class SevenServiceMigration extends Command
{
    protected $signature = 'safetech:seven-service-preflight {--audit-collisions} {--execute}';

    protected $description = 'Audit or consolidate services while preserving historical records';

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
                $this->warn("Missing canonical target (created during execute): {$slug}");
            }
        }

        if ($unknown->isNotEmpty()) {
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
            if (! in_array($targetSlug, $canonicalSlugs, true)) {
                $this->error("Invalid destination for {$source->slug}: {$targetSlug}");

                return self::FAILURE;
            }

            // A canonical target may be created only by the future migration.
            $targetId = $services->get($targetSlug)?->id;
            $landingCollisions = 0;
            $linkedLandings = 0;
            $quotes = 0;
            $quoteCollisions = 0;

            if (Schema::hasTable('local_service_landings')) {
                $linkedLandings = DB::table('local_service_landings')->where('service_id', $source->id)->count();

                if ($targetId !== null) {
                    $landingCollisions = DB::table('local_service_landings as source')
                        ->join('local_service_landings as target', function ($join) use ($targetId): void {
                            $join->on('source.location_slug', '=', 'target.location_slug')
                                ->where('target.service_id', $targetId);
                        })
                        ->where('source.service_id', $source->id)
                        ->count();
                }
            }

            if (Schema::hasTable('quote_catalog_items')) {
                $quotes = DB::table('quote_catalog_items')->where('service_id', $source->id)->count();

                if ($targetId !== null) {
                    $quoteCollisions = DB::table('quote_catalog_items as source')
                        ->join('quote_catalog_items as target', function ($join) use ($targetId): void {
                            $join->on('source.component_key', '=', 'target.component_key')
                                ->where('target.service_id', $targetId);
                        })
                        ->where('source.service_id', $source->id)
                        ->count();
                }
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

        if ((bool) $this->option('execute')) {
            return $this->executePreservingLegacy($canonicalSlugs, $aliases);
        }

        $this->warn('READ-ONLY preflight completed. No records were updated or deleted.');
        $this->warn('Legacy SEO content, media and quote items must be preserved during consolidation.');

        return self::SUCCESS;
    }

    /**
     * Non-destructive public consolidation: legacy records are retained as
     * historical archives. This deliberately does not rewrite quote pricing,
     * calculator profiles, estimates, analytics, media ownership or SEO copy.
     */
    private function executePreservingLegacy(array $canonicalSlugs, array $aliases): int
    {
        if (! Schema::hasTable('local_service_landings')
            || ! Schema::hasTable('local_service_landing_project')
            || ! Schema::hasTable('quote_catalog_items')) {
            $this->error('Required production tables are missing.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($canonicalSlugs, $aliases): void {
            // Seed only missing canonical definitions; existing CMS fields are
            // preserved by ServiceCatalogSeeder::fillMissing().
            app(ServiceCatalogSeeder::class)->run();

            $services = DB::table('services')->get(['id', 'slug'])->keyBy('slug');
            foreach ($canonicalSlugs as $slug) {
                if (! $services->has($slug)) {
                    throw new \RuntimeException("Missing canonical service after seeding: {$slug}");
                }
            }

            foreach ($services as $source) {
                if (in_array($source->slug, $canonicalSlugs, true)) {
                    continue;
                }

                $targetSlug = $aliases[$source->slug] ?? null;
                if (! $targetSlug || ! in_array($targetSlug, $canonicalSlugs, true)) {
                    throw new \RuntimeException("Unmapped legacy service: {$source->slug}");
                }

                $targetId = $services->get($targetSlug)->id;
                $landings = DB::table('local_service_landings')
                    ->where('service_id', $source->id)
                    ->get();

                foreach ($landings as $landing) {
                    $targetLandingId = DB::table('local_service_landings')
                        ->where('service_id', $targetId)
                        ->where('location_slug', $landing->location_slug)
                        ->value('id');

                    if (! $targetLandingId) {
                        // Reassigning a unique, non-colliding landing preserves
                        // its ID, all original copy, translations and media.
                        DB::table('local_service_landings')
                            ->where('id', $landing->id)
                            ->update(['service_id' => $targetId, 'updated_at' => now()]);

                        continue;
                    }

                    // Colliding source landing is archived unchanged, including
                    // OG media and all localized SEO fields. Merge its project
                    // associations into the public canonical landing.
                    $projectIds = DB::table('local_service_landing_project')
                        ->where('landing_id', $landing->id)
                        ->pluck('project_id');

                    foreach ($projectIds as $projectId) {
                        DB::table('local_service_landing_project')->insertOrIgnore([
                            'landing_id' => $targetLandingId,
                            'project_id' => $projectId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Do not delete services: historical quotes, calculator inputs,
                // analytics, FAQs, media and colliding SEO landings remain valid.
                DB::table('services')->where('id', $source->id)->update([
                    'is_published' => false,
                    'updated_at' => now(),
                ]);
            }

            // Existing canonical services may be unpublished by editorial choice;
            // do not override that choice or overwrite their CMS content.
        });

        $this->info('Public catalog consolidation completed without deleting historical records.');
        $this->warn('Legacy SEO pages and quote rows are retained. Review redirect and archive coverage before deployment.');

        return self::SUCCESS;
    }
}
