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
    /** @var array<string, array{title: string, description: string}> */
    private const SERVICE_FINGERPRINTS = [
        'cctv' => [
            'title' => 'CCTV installation and monitoring',
            'description' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.',
        ],
        'networking' => [
            'title' => 'Network Infrastructure',
            'description' => 'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.',
        ],
        'access-control' => [
            'title' => 'Access control and attendance systems',
            'description' => 'Card, PIN, biometric, and face-recognition access control for secure workplaces.',
        ],
        'server-infrastructure' => [
            'title' => 'Server infrastructure and virtualization',
            'description' => 'Server deployment, virtualization, storage, backup, and infrastructure monitoring.',
        ],
        'it-support' => [
            'title' => 'Managed IT support for business',
            'description' => 'Remote and on-site IT support, monitoring, asset management, and practical SLA plans.',
        ],
    ];

    /** @var array<int, string> */
    private const SERVICE_CATEGORY_SLUGS = [
        'security-systems',
        'network-infrastructure',
        'server-infrastructure',
        'it-support',
    ];

    /** @var array<string, array{title: string, description: string}> */
    private const PROJECT_FINGERPRINTS = [
        'office-network-upgrade' => [
            'title' => 'Office Network Upgrade',
            'description' => 'A complete network refresh with structured cabling, managed switching, and Wi-Fi coverage.',
        ],
    ];

    /** @var array<int, string> */
    private const PROJECT_CATEGORY_SLUGS = ['offices'];

    protected $signature = 'cms:remove-demo-content
                            {--force : Remove matching demo records without confirmation}';

    protected $description = 'Remove untouched bundled demo services and projects while preserving edited CMS records';

    public function handle(): int
    {
        $services = collect(self::SERVICE_FINGERPRINTS)
            ->map(function (array $fingerprint, string $slug): ?Service {
                return Service::query()
                    ->where('slug', $slug)
                    ->where('title', $fingerprint['title'])
                    ->where('description', $fingerprint['description'])
                    ->whereNull('hero_image')
                    ->whereColumn('created_at', 'updated_at')
                    ->whereDoesntHave('media')
                    ->first();
            })
            ->filter()
            ->sortBy('slug')
            ->values();
        $projects = collect(self::PROJECT_FINGERPRINTS)
            ->map(function (array $fingerprint, string $slug): ?Project {
                return Project::query()
                    ->where('slug', $slug)
                    ->where('title', $fingerprint['title'])
                    ->where('description', $fingerprint['description'])
                    ->whereNull('image')
                    ->whereColumn('created_at', 'updated_at')
                    ->whereDoesntHave('media')
                    ->first();
            })
            ->filter()
            ->sortBy('slug')
            ->values();
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
        $this->warn('Only untouched bundled records without uploaded media and their unused categories will be removed. Edited CMS records are preserved.');

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
