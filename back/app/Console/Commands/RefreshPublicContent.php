<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
use App\Support\CanonicalSeedTombstones;
use App\Support\MultilingualContent;
use Database\Seeders\ConsultationCopySeeder;
use Database\Seeders\GoogleBusinessServiceDefinitions;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\PrivacyPageSeeder;
use Database\Seeders\ProductionContentSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class RefreshPublicContent extends Command
{
    private const VERSION = '2026-09-30-canonical-seo-v4';

    private const STATE_KEY = 'system_content_seed_version';

    protected $signature = 'safetech:refresh-public-content
        {--force : Allow the versioned destructive cleanup when it has not run yet}
        {--reset : Repeat the destructive cleanup even when the current version is already applied}';

    protected $description = 'Rebuild canonical SafeTech service/SEO content while preserving projects, project categories and operational records';

    public function handle(): int
    {
        $currentVersion = $this->currentVersion();
        $needsReset = (bool) $this->option('reset') || $currentVersion !== self::VERSION;

        if (! $needsReset) {
            $this->info('Canonical public content is already on '.self::VERSION.'. Running non-destructive seed refresh.');
            $this->callSeeder(rebuild: false);

            return self::SUCCESS;
        }

        if (! (bool) $this->option('force')) {
            $this->error('A destructive public-content rebuild is required. Re-run with --force.');

            return self::FAILURE;
        }

        $this->warn('Rebuilding services, service categories, Local SEO pages, service FAQs and static SEO metadata.');
        $this->line('Projects, project categories, users, leads, estimates, quote pricing, integrations and project media are preserved.');

        DB::transaction(function (): void {
            $this->clearCanonicalTombstones();

            // Make sure every canonical redirect target exists before old aliases
            // are removed or their operational references are moved.
            app(ServiceCatalogSeeder::class)->run();

            $projectLinks = $this->snapshotProjectLinks();
            $this->remapAliasReferences();

            if (Schema::hasTable('local_service_landing_project')) {
                DB::table('local_service_landing_project')->delete();
            }
            if (Schema::hasTable('local_service_landings')) {
                DB::table('local_service_landings')->delete();
            }

            if (Schema::hasTable('faqs')) {
                DB::table('faqs')
                    ->whereNotNull('service_id')
                    ->orWhere('context', 'contact')
                    ->delete();
            }

            if (Schema::hasTable('seo_pages')) {
                DB::table('seo_pages')->delete();
            }

            $this->resetCanonicalTranslationEntries();
            $this->enforceServiceAreaContact();
            $this->removeNonCanonicalServices();

            if (Schema::hasTable('category_for_services')) {
                DB::table('category_for_services')->delete();
            }

            $this->callSeeder(rebuild: true);
            $this->restoreProjectLinks($projectLinks);
            $this->storeVersion();
        });

        $this->info('Canonical public content rebuild completed: '.self::VERSION);
        $this->line('Projects and project categories were not deleted or rewritten.');

        return self::SUCCESS;
    }

    private function callSeeder(bool $rebuild): void
    {
        $seeder = app(ProductionContentSeeder::class);
        $seeder->setContainer(app());

        if ($this->output) {
            $seeder->setCommand($this);
        }

        if ($rebuild) {
            $seeder->rebuild();

            return;
        }

        $seeder->run();
    }

    private function currentVersion(): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        $value = DB::table('site_settings')
            ->where('key', self::STATE_KEY)
            ->value('value');
        $decoded = $this->decodeJson($value);

        return is_string($decoded['version'] ?? null)
            ? $decoded['version']
            : null;
    }

    private function storeVersion(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        DB::table('site_settings')->updateOrInsert(
            ['key' => self::STATE_KEY],
            [
                'group' => 'system',
                'value' => $this->encodeJson([
                    'version' => self::VERSION,
                    'applied_at' => now()->toAtomString(),
                ]),
                'is_public' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    /** @return Collection<int, object> */
    private function snapshotProjectLinks(): Collection
    {
        if (
            ! Schema::hasTable('local_service_landing_project')
            || ! Schema::hasTable('local_service_landings')
            || ! Schema::hasTable('services')
        ) {
            return collect();
        }

        return DB::table('local_service_landing_project as pivot')
            ->join('local_service_landings as landing', 'landing.id', '=', 'pivot.landing_id')
            ->join('services as service', 'service.id', '=', 'landing.service_id')
            ->select([
                'service.slug as service_slug',
                'landing.location_slug',
                'pivot.project_id',
            ])
            ->get();
    }

    /** @param Collection<int, object> $links */
    private function restoreProjectLinks(Collection $links): void
    {
        if (
            $links->isEmpty()
            || ! Schema::hasTable('local_service_landing_project')
            || ! Schema::hasTable('projects')
        ) {
            return;
        }

        foreach ($links as $link) {
            $landingId = DB::table('local_service_landings as landing')
                ->join('services as service', 'service.id', '=', 'landing.service_id')
                ->where('service.slug', $link->service_slug)
                ->where('landing.location_slug', $link->location_slug)
                ->value('landing.id');

            if (! $landingId) {
                continue;
            }

            if (! DB::table('projects')->where('id', $link->project_id)->exists()) {
                continue;
            }

            DB::table('local_service_landing_project')->insertOrIgnore([
                'landing_id' => $landingId,
                'project_id' => $link->project_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function remapAliasReferences(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        foreach (GoogleBusinessServiceDefinitions::CANONICAL_ALIASES as $alias => $canonical) {
            $aliasId = DB::table('services')->where('slug', $alias)->value('id');
            $canonicalId = DB::table('services')->where('slug', $canonical)->value('id');

            if (! $aliasId || ! $canonicalId || $aliasId === $canonicalId) {
                continue;
            }

            foreach (['estimates', 'analytics_events'] as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'service_id')) {
                    DB::table($table)
                        ->where('service_id', $aliasId)
                        ->update(['service_id' => $canonicalId]);
                }
            }

            if (Schema::hasTable('quote_catalog_items')) {
                $items = DB::table('quote_catalog_items')
                    ->where('service_id', $aliasId)
                    ->get();

                foreach ($items as $item) {
                    $duplicate = DB::table('quote_catalog_items')
                        ->where('service_id', $canonicalId)
                        ->where('component_key', $item->component_key)
                        ->exists();

                    if ($duplicate) {
                        DB::table('quote_catalog_items')->where('id', $item->id)->delete();

                        continue;
                    }

                    DB::table('quote_catalog_items')
                        ->where('id', $item->id)
                        ->update([
                            'service_id' => $canonicalId,
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    private function removeNonCanonicalServices(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        $canonicalSlugs = ServiceCatalogSeeder::canonicalServiceSlugs();

        if ($canonicalSlugs === []) {
            throw new \RuntimeException('Canonical service list is empty; refusing to delete services.');
        }

        DB::table('services')
            ->whereNotIn('slug', $canonicalSlugs)
            ->delete();
    }

    private function clearCanonicalTombstones(): void
    {
        if (! Schema::hasTable('seed_deletion_tombstones')) {
            return;
        }

        DB::table('seed_deletion_tombstones')
            ->whereIn('type', [
                CanonicalSeedTombstones::CATEGORY,
                CanonicalSeedTombstones::FAQ,
                CanonicalSeedTombstones::SEO_PAGE,
                CanonicalSeedTombstones::SERVICE,
                CanonicalSeedTombstones::SITE_SETTING,
                CanonicalSeedTombstones::TRANSLATION_ENTRY,
            ])
            ->delete();
    }

    private function resetCanonicalTranslationEntries(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $setting = SiteSetting::query()->where('key', 'translations')->first();

        if (! $setting || ! is_array($setting->value)) {
            return;
        }

        $canonicalKeys = array_flip(array_values(array_unique([
            ...PageContentSeeder::canonicalTranslationKeys(),
            ...ConsultationCopySeeder::canonicalTranslationKeys(),
            ...PrivacyPageSeeder::canonicalTranslationKeys(),
            ...ServiceCatalogSeeder::canonicalTranslationKeys(),
        ])));

        $map = MultilingualContent::mapFrom($setting->value);
        $map = array_filter(
            $map,
            static fn (string $key): bool => ! isset($canonicalKeys[$key]),
            ARRAY_FILTER_USE_KEY,
        );

        $value = $setting->value;
        $value['entries'] = MultilingualContent::entriesFromMap($map);

        DB::table('site_settings')
            ->where('id', $setting->id)
            ->update([
                'value' => $this->encodeJson($value),
                'updated_at' => now(),
            ]);
    }

    private function enforceServiceAreaContact(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $row = DB::table('site_settings')->where('key', 'contact')->first();

        if (! $row) {
            return;
        }

        $value = $this->decodeJson($row->value ?? null);
        $value['service_area_business'] = true;
        $value['address'] = '';
        $value['address_en'] = '';
        $value['address_ru'] = '';

        DB::table('site_settings')
            ->where('id', $row->id)
            ->update([
                'value' => $this->encodeJson($value),
                'updated_at' => now(),
            ]);
    }

    /** @return array<string, mixed> */
    private function decodeJson(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function encodeJson(array $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: '{}';
    }
}
