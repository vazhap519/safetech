<?php

namespace App\Console\Commands;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

final class LocalSeoAudit extends Command
{
    protected $signature = 'safetech:local-seo-audit {--strict : Fail on missing indexable service coverage or localized metadata}';

    protected $description = 'Audit published services, Local SEO URLs, translations and genuine project links without changing CMS data';

    public function handle(): int
    {
        $published = Service::query()->publiclyVisible()->get();
        $indexable = LocalServiceLanding::query()->publiclyVisible()
            ->where('noindex', false)
            ->with('service')
            ->get();
        $coveredIds = $indexable->pluck('service_id')->unique()->all();
        $missing = $published->whereNotIn('id', $coveredIds)->values();
        $withoutProjects = LocalServiceLanding::query()->publiclyVisible()
            ->where('noindex', false)
            ->whereDoesntHave('publicProjects')
            ->with('service')
            ->get();

        $problems = [];
        $this->info(sprintf(
            'Local SEO technical coverage: %d/%d published services, %d indexable Local pages, %d with no linked public project.',
            $published->count() - $missing->count(),
            $published->count(),
            $indexable->count(),
            $withoutProjects->count(),
        ));

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
