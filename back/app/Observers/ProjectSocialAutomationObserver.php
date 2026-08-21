<?php

namespace App\Observers;

use App\Jobs\PublishProjectToSocialAutomation;
use App\Models\Project;

class ProjectSocialAutomationObserver
{
    public function created(Project $project): void
    {
        $this->dispatchIfReady($project);
    }

    public function updated(Project $project): void
    {
        if (! $project->wasChanged(['is_published', 'published_at'])) {
            return;
        }

        $this->dispatchIfReady($project);
    }

    private function dispatchIfReady(Project $project): void
    {
        if (! config('social_automation.project_publish.enabled', false)) {
            return;
        }

        if (! $project->isReadyForSocialShare()) {
            return;
        }

        $delaySeconds = max(
            0,
            (int) config('social_automation.project_publish.delay_seconds', 120),
        );

        PublishProjectToSocialAutomation::dispatch($project->getKey())
            ->delay(now()->addSeconds($delaySeconds))
            ->afterCommit();
    }
}
