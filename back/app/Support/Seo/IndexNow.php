<?php

namespace App\Support\Seo;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class IndexNow
{
    private const ENDPOINT = 'https://api.indexnow.org/indexnow';

    public static function submitModel(Model $model): void
    {
        $paths = match (true) {
            $model instanceof Service => self::servicePaths($model),
            $model instanceof Project => self::projectPaths($model),
            $model instanceof LocalServiceLanding => self::localLandingPaths($model),
            default => [],
        };

        self::submitPaths($paths);
    }

    /** @param array<int, string> $paths */
    public static function submitPaths(array $paths): void
    {
        if (! app()->environment('production')) {
            return;
        }

        $key = self::key();
        if ($key === '') {
            return;
        }

        $host = parse_url((string) config('app.frontend_url', 'https://safetech.ge'), PHP_URL_HOST) ?: 'safetech.ge';
        $base = 'https://'.$host;
        $urls = collect($paths)
            ->filter()
            ->map(fn (string $path): string => $base.'/'.ltrim($path, '/'))
            ->unique()
            ->values()
            ->all();

        if ($urls === []) {
            return;
        }

        try {
            $response = Http::timeout(8)->retry(2, 250)->post(self::ENDPOINT, [
                'host' => $host,
                'key' => $key,
                'keyLocation' => $base.'/'.$key,
                'urlList' => $urls,
            ]);

            if (! $response->successful() && $response->status() !== 202) {
                Log::warning('IndexNow submission failed.', [
                    'status' => $response->status(),
                    'urls' => $urls,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('IndexNow submission error.', [
                'message' => $exception->getMessage(),
                'urls' => $urls,
            ]);
        }
    }

    private static function key(): string
    {
        $value = SiteSetting::query()->where('key', 'integrations')->value('value');
        $key = is_array($value) ? trim((string) ($value['indexnow_key'] ?? '')) : '';

        return preg_match('/^[a-f0-9]{32,128}$/i', $key) === 1 ? $key : '';
    }

    /** @return array<int, string> */
    private static function servicePaths(Service $service): array
    {
        if (! $service->is_published || blank($service->slug)) {
            return [];
        }

        return self::localizedPaths('/services/'.$service->slug);
    }

    /** @return array<int, string> */
    private static function projectPaths(Project $project): array
    {
        if (! $project->is_published || blank($project->slug) || ($project->published_at && $project->published_at->isFuture())) {
            return [];
        }

        return self::localizedPaths('/projects/'.$project->slug);
    }

    /** @return array<int, string> */
    private static function localLandingPaths(LocalServiceLanding $landing): array
    {
        if (
            ! $landing->is_published ||
            $landing->noindex ||
            blank($landing->location_slug) ||
            ($landing->published_at && $landing->published_at->isFuture())
        ) {
            return [];
        }

        $service = $landing->relationLoaded('service') ? $landing->service : $landing->service()->first();
        if (! $service?->is_published || blank($service->slug)) {
            return [];
        }

        return self::localizedPaths('/services/'.$service->slug.'/'.$landing->location_slug);
    }

    /** @return array<int, string> */
    private static function localizedPaths(string $path): array
    {
        return [$path, '/en'.$path, '/ru'.$path];
    }
}
