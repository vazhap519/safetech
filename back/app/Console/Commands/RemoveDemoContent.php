<?php

namespace App\Console\Commands;

use App\Models\CategoryForService;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Support\FrontendRevalidator;
use App\Support\PublicContentCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDemoContent extends Command
{
    /** @var array<int, string> */
    private const SERVICE_SLUGS = [
        'cctv',
        'networking',
        'access-control',
        'server-infrastructure',
        'it-support',
    ];

    /** @var array<int, string> */
    private const SERVICE_CATEGORY_SLUGS = [
        'security-systems',
        'network-infrastructure',
        'server-infrastructure',
        'it-support',
    ];

    /** @var array<int, string> */
    private const PROJECT_SLUGS = ['office-network-upgrade'];

    /** @var array<int, string> */
    private const PROJECT_CATEGORY_SLUGS = ['offices'];

    protected $signature = 'cms:remove-demo-content
                            {--force : Remove matching demo records without confirmation}';

    protected $description = 'Remove bundled demo services and projects while preserving records with other slugs';

    public function handle(): int
    {
        $services = Service::query()
            ->whereIn('slug', self::SERVICE_SLUGS)
            ->orderBy('slug')
            ->get();
        $projects = Project::query()
            ->whereIn('slug', self::PROJECT_SLUGS)
            ->orderBy('slug')
            ->get();
        $serviceCategories = CategoryForService::query()
            ->whereIn('slug', self::SERVICE_CATEGORY_SLUGS)
            ->whereDoesntHave('services')
            ->orderBy('slug')
            ->get();
        $projectCategories = ProjectCategory::query()
            ->whereIn('slug', self::PROJECT_CATEGORY_SLUGS)
            ->whereDoesntHave('projects')
            ->orderBy('slug')
            ->get();

        if ($services->isEmpty()
            && $projects->isEmpty()
            && $serviceCategories->isEmpty()
            && $projectCategories->isEmpty()) {
            $this->info('No bundled demo content was found.');

            return self::SUCCESS;
        }

        $rows = $services
            ->map(fn (Service $service): array => [
                'Service',
                $service->slug,
                $service->name ?: $service->title,
            ])
            ->concat($projects->map(fn (Project $project): array => [
                'Project',
                $project->slug,
                $project->name ?: $project->title,
            ]))
            ->concat($serviceCategories->map(fn (CategoryForService $category): array => [
                'Unused service category',
                $category->slug,
                $category->name,
            ]))
            ->concat($projectCategories->map(fn (ProjectCategory $category): array => [
                'Unused project category',
                $category->slug,
                $category->name,
            ]))
            ->values()
            ->all();

        $this->table(['Type', 'Slug', 'Title'], $rows);
        $this->warn('Only exact bundled demo slugs and their unused categories will be removed. Records with other slugs are preserved.');

        if (! $this->option('force') && ! $this->confirm('Remove these demo records?', false)) {
            $this->info('No records were changed.');

            return self::SUCCESS;
        }

        $deleted = DB::transaction(function () use ($services, $projects): int {
            $deleted = 0;

            foreach ($services as $service) {
                $service->delete();
                $deleted++;
            }

            foreach ($projects as $project) {
                $project->delete();
                $deleted++;
            }

            $deleted += CategoryForService::query()
                ->whereIn('slug', self::SERVICE_CATEGORY_SLUGS)
                ->whereDoesntHave('services')
                ->delete();
            $deleted += ProjectCategory::query()
                ->whereIn('slug', self::PROJECT_CATEGORY_SLUGS)
                ->whereDoesntHave('projects')
                ->delete();

            return $deleted;
        });

        PublicContentCache::flush();
        FrontendRevalidator::revalidate('cms');

        $this->info("Removed {$deleted} demo records. CMS records with other slugs were not changed.");

        return self::SUCCESS;
    }
}
