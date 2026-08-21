<?php

namespace App\Console\Commands;

use App\Jobs\PublishProjectToSocialAutomation;
use App\Models\Project;
use Illuminate\Console\Command;

class PublishProjectToSocial extends Command
{
    protected $signature = 'social:publish-project
        {project? : Project ID or slug. Omit it to use the latest published project.}
        {--force : Clear the existing social share marker and publish again.}';

    protected $description = 'Queue a published SafeTech project for the n8n social automation workflow';

    public function handle(): int
    {
        $project = $this->resolveProject();

        if (! $project) {
            $this->error('No matching published project was found.');

            return self::FAILURE;
        }

        if (! $project->is_published) {
            $this->error("Project #{$project->getKey()} is not published.");

            return self::FAILURE;
        }

        if ($this->option('force') && $project->social_shared_at !== null) {
            $project->forceFill(['social_shared_at' => null])->saveQuietly();
        }

        if ($project->social_shared_at !== null) {
            $this->warn(
                "Project #{$project->getKey()} was already sent to social automation at "
                .$project->social_shared_at->toDateTimeString()
                .'. Use --force to publish it again.'
            );

            return self::SUCCESS;
        }

        if (! $project->isReadyForSocialShare()) {
            $this->error('The project is not publicly shareable yet. Check published_at, slug, title/name and description.');

            return self::FAILURE;
        }

        PublishProjectToSocialAutomation::dispatch($project->getKey());

        $this->info(
            "Queued project #{$project->getKey()} ({$project->slug}) for n8n social publishing."
        );

        return self::SUCCESS;
    }

    private function resolveProject(): ?Project
    {
        $identifier = $this->argument('project');

        if ($identifier !== null && $identifier !== '') {
            return Project::query()
                ->where('id', $identifier)
                ->orWhere('slug', $identifier)
                ->first();
        }

        return Project::query()
            ->where('is_published', true)
            ->where(function ($query): void {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->first();
    }
}
