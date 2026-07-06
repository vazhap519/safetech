<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnalyticsEventRequest;
use App\Models\AnalyticsEvent;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyticsEventController extends Controller
{
    public function __invoke(
        StoreAnalyticsEventRequest $request,
    ): JsonResponse {
        $validated = $request->validated();
        $pagePath = $this->normalizePagePath($validated['page_path'] ?? null);
        $serviceSlug = $validated['service_slug'] ?? $this->extractServiceSlug($pagePath);
        $service = $serviceSlug
            ? Service::query()->where('slug', $serviceSlug)->first()
            : null;

        $event = AnalyticsEvent::query()->create([
            'event_type' => $validated['event_type'],
            'service_id' => $service?->id,
            'service_slug' => $service?->slug ?? $serviceSlug,
            'page_path' => $pagePath,
            'locale' => $validated['locale'] ?? null,
            'visitor_hash' => $this->hashVisitor(
                $request,
                $validated['visitor_id'] ?? null,
            ),
            'ip_hash' => $this->hashIp($request),
            'user_agent' => Str::limit($request->userAgent() ?? '', 1000, ''),
            'meta' => $validated['meta'] ?? null,
        ]);

        return response()->json([
            'data' => [
                'tracked' => true,
                'eventType' => $event->event_type,
            ],
        ], 201);
    }

    private function normalizePagePath(?string $pagePath): ?string
    {
        if (! is_string($pagePath) || $pagePath === '') {
            return null;
        }

        $trimmed = trim($pagePath);

        if ($trimmed === '') {
            return null;
        }

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            $parsedPath = parse_url($trimmed, PHP_URL_PATH);

            if (! is_string($parsedPath) || $parsedPath === '') {
                return null;
            }

            $query = parse_url($trimmed, PHP_URL_QUERY);

            return Str::limit(
                $query ? "{$parsedPath}?{$query}" : $parsedPath,
                255,
                '',
            );
        }

        $normalized = str_starts_with($trimmed, '/') ? $trimmed : "/{$trimmed}";

        return Str::limit($normalized, 255, '');
    }

    private function extractServiceSlug(?string $pagePath): ?string
    {
        if (! $pagePath) {
            return null;
        }

        if (! preg_match('#^/services/([^/?#]+)#', $pagePath, $matches)) {
            return null;
        }

        return $matches[1] ?: null;
    }

    private function hashVisitor(Request $request, ?string $visitorId): string
    {
        if (is_string($visitorId) && trim($visitorId) !== '') {
            return hash('sha256', config('app.key') . '|analytics|visitor|' . trim($visitorId));
        }

        return hash(
            'sha256',
            config('app.key') . '|analytics|fallback|' . ($request->ip() ?? 'unknown') . '|' . ($request->userAgent() ?? 'unknown'),
        );
    }

    private function hashIp(Request $request): ?string
    {
        $ip = $request->ip();

        if (! $ip) {
            return null;
        }

        return hash('sha256', config('app.key') . '|analytics|ip|' . $ip);
    }
}
