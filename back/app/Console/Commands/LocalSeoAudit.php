<?php

namespace App\Console\Commands;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

final class LocalSeoAudit extends Command
{
    protected $signature = 'safetech:local-seo-audit {--strict : Fail on missing indexable service coverage or localized metadata} {--list-unlinked : List Local pages without linked public projects for editorial review} {--unlinked-limit=25 : Maximum unlinked Local pages to display (0 means all)}';

    protected $description = 'Audit published services, Local SEO URLs, translations and genuine project links without changing CMS data';

    public function handle(): int
    {
        $published = Service::query()->publiclyVisible()->get();
        // New GBP catalog entries are visible to visitors but intentionally noindexed
        // until each page has substantive editorial copy. They should not fail
        // local SEO coverage while their SEO opt-out remains active.
        $indexableServices = $published->filter(
            fn (Service $service): bool => ! (bool) data_get($service->seo, 'noindex', false),
        );
        $indexable = LocalServiceLanding::query()->publiclyVisible()
            ->where('noindex', false)
            ->with('service')
            ->get();
        $coveredIds = $indexable->pluck('service_id')->unique()->all();
        $missing = $indexableServices->whereNotIn('id', $coveredIds)->values();
        $withoutProjects = LocalServiceLanding::query()->publiclyVisible()
            ->where('noindex', false)
            ->whereDoesntHave('publicProjects')
            ->with('service')
            ->get();

        $problems = [];
        $this->info(sprintf(
            'Local SEO technical coverage: %d/%d indexable published services, %d indexable Local pages, %d with no linked public project.',
            $indexableServices->count() - $missing->count(),
            $indexableServices->count(),
            $indexable->count(),
            $withoutProjects->count(),
        ));

        if ((bool) $this->option('list-unlinked')) {
            $limit = filter_var($this->option('unlinked-limit'), FILTER_VALIDATE_INT);
            if ($limit === false || $limit < 0) {
                $this->error('--unlinked-limit must be a non-negative integer (0 means all).');

                return self::FAILURE;
            }

            $rows = $withoutProjects
                ->sortBy(fn (LocalServiceLanding $landing): string => $landing->location_slug.'/'.$landing->service->slug)
                ->values();
            if ($limit > 0) {
                $rows = $rows->take($limit);
            }

            $this->table(
                ['Location', 'Service', 'Local page path'],
                $rows->map(fn (LocalServiceLanding $landing): array => [
                    $landing->location_slug,
                    $landing->service->slug,
                    "/services/{$landing->service->slug}/{$landing->location_slug}",
                ])->all(),
            );
            $this->line(sprintf(
                'Showing %d of %d Local pages without linked public projects. This is an editorial review list, not an SEO error.',
                $rows->count(),
                $withoutProjects->count(),
            ));
        }

        foreach ($missing as $service) {
            $problems[] = "Missing indexable Local landing: {$service->slug}";
        }

        foreach ($indexable as $landing) {
            $path = "/services/{$landing->service->slug}/{$landing->location_slug}";

            foreach (['ka', 'en', 'ru'] as $locale) {
                foreach (['title', 'content', 'seoTitle', 'seoDescription'] as $field) {
                    $root = match ($field) {
                        'seoTitle' => 'seo_title',
                        'seoDescription' => 'seo_description',
                        default => $field,
                    };
                    $value = trim((string) Arr::get(
                        $landing->translations ?? [],
                        "fields.{$field}.{$locale}",
                        $locale === 'ka' ? $landing->{$root} : '',
                    ));
                    if ($value === '') {
                        $problems[] = "{$path} missing {$locale} {$field}";
                    }
                }
            }
        }

        // Reused localized titles/descriptions can make distinct location pages
        // indistinguishable to search engines. Report candidates for editorial
        // review; do not automatically noindex, rewrite or unpublish pages.
        foreach (['ka', 'en', 'ru'] as $locale) {
            foreach (['seoTitle' => 'seo_title', 'seoDescription' => 'seo_description'] as $field => $root) {
                $groups = [];

                foreach ($indexable as $landing) {
                    $value = trim((string) Arr::get(
                        $landing->translations ?? [],
                        "fields.{$field}.{$locale}",
                        $locale === 'ka' ? $landing->{$root} : '',
                    ));
                    $normalized = mb_strtolower(preg_replace('/\\s+/u', ' ', $value) ?? $value);

                    if ($normalized === '') {
                        continue;
                    }

                    $groups[$normalized][] = "/services/{$landing->service->slug}/{$landing->location_slug}";
                }

                foreach ($groups as $paths) {
                    if (count($paths) < 2) {
                        continue;
                    }

                    $this->warn(sprintf(
                        'Duplicate %s %s across %d indexable Local pages: %s',
                        $locale,
                        $field,
                        count($paths),
                        implode(', ', $paths),
                    ));
                }
            }
        }

        foreach ($problems as $problem) {
            $this->error($problem);
        }

        $this->warn('Linked proof is a separate editorial task: never invent or misattribute a completed project to clear a warning.');
        $this->line('Indexable here means published and noindex=false, NOT verified indexing or a Google ranking.');
        $this->line('Check Google Search Console URL Inspection, Core Web Vitals and actual organic conversions after deployment.');

        if ((bool) $this->option('strict') && $problems !== []) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
