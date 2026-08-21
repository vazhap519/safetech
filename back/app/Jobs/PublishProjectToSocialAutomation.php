<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PublishProjectToSocialAutomation implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 120, 300];

    public int $timeout = 45;

    public int $uniqueFor = 600;

    public function __construct(public int $projectId) {}

    public function uniqueId(): string
    {
        return "project-social-publish:{$this->projectId}";
    }

    public function handle(): void
    {
        if (! config('social_automation.project_publish.enabled', false)) {
            return;
        }

        $project = Project::query()
            ->with('projectCategory')
            ->find($this->projectId);

        if (! $project || ! $project->isReadyForSocialShare()) {
            return;
        }

        $webhookUrl = trim((string) config('social_automation.project_publish.webhook_url'));

        if ($webhookUrl === '') {
            Log::warning('Project social publish skipped because the n8n webhook URL is empty.', [
                'project_id' => $this->projectId,
            ]);

            return;
        }

        $request = Http::acceptJson()
            ->asJson()
            ->connectTimeout((int) config('social_automation.project_publish.connect_timeout', 3))
            ->timeout((int) config('social_automation.project_publish.timeout', 30));

        $token = trim((string) config('social_automation.project_publish.token'));

        if ($token !== '') {
            $request = $request->withHeaders([
                'X-SafeTech-Webhook-Token' => $token,
            ]);
        }

        $frontendBase = rtrim((string) (config('app.frontend_url') ?: 'https://safetech.ge'), '/');
        $apiBase = rtrim((string) config('app.url', 'https://api.safetech.ge'), '/');

        $response = $request->post($webhookUrl, [
            'event' => 'project.published',
            'source' => 'safetech.ge',
            'idempotency_key' => "project:{$project->getKey()}:published",
            'project_id' => $project->getKey(),
            'id' => $project->getKey(),
            'slug' => $project->slug,
            'project_slug' => $project->slug,
            'name' => $project->name ?: $project->title,
            'title' => $project->title ?: $project->name,
            'description' => $project->description ?: $project->excerpt ?: $project->content,
            'category' => [
                'name' => $project->category_name,
                'slug' => $project->category_slug,
            ],
            'cover_url' => $project->cover_url,
            'api_url' => "{$apiBase}/api/projects/{$project->slug}",
            'project_url' => "{$frontendBase}/projects/{$project->slug}",
            'published_at' => $project->published_at?->toIso8601String()
                ?? $project->created_at?->toIso8601String()
                ?? now()->toIso8601String(),
        ]);

        if (! $response->successful()) {
            Log::warning('n8n rejected the project publish webhook.', [
                'project_id' => $project->getKey(),
                'status' => $response->status(),
                'response' => mb_substr($response->body(), 0, 1000),
            ]);

            throw new RuntimeException(
                "Project publish webhook returned HTTP {$response->status()}."
            );
        }

        $project->forceFill([
            'social_shared_at' => now(),
        ])->saveQuietly();
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Project social publish failed after all retries.', [
            'project_id' => $this->projectId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
