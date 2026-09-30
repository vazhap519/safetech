<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocalServiceLandingResource;
use App\Http\Resources\LocalServiceLandingSitemapResource;
use App\Http\Resources\LocalServiceLandingSummaryResource;
use App\Models\LocalServiceLanding;
use App\Support\MultilingualContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class LocalServiceLandingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $serviceSlug = trim($request->string('service')->toString());
        $locationSlug = trim($request->string('location')->toString());
        $view = $request->string('view')->toString();

        $query = LocalServiceLanding::query()
            ->publiclyVisible();

        if ($serviceSlug !== '') {
            $query->whereHas(
                'service',
                fn (Builder $service): Builder => $service->where('slug', $serviceSlug),
            );
        }

        if ($locationSlug !== '') {
            $query->where('location_slug', $locationSlug);
        }

        if ($view === 'sitemap') {
            $landings = $query
                ->with(['service:id,slug'])
                ->get([
                    'id',
                    'service_id',
                    'location_slug',
                    'noindex',
                    'updated_at',
                    'sort_order',
                ]);

            return LocalServiceLandingSitemapResource::collection($landings);
        }

        if ($view === 'summary') {
            $landings = $query
                ->with([
                    'service:id,slug,name,title,translations',
                    'publicProjects' => fn (BelongsToMany $projects): BelongsToMany => $projects
                        ->select(['projects.id', 'projects.slug']),
                ])
                ->get([
                    'id',
                    'service_id',
                    'location_slug',
                    'location_name',
                    'title',
                    'seo_title',
                    'translations',
                    'noindex',
                    'updated_at',
                    'sort_order',
                ])
                ->filter(fn (LocalServiceLanding $landing): bool => $this->hasLocaleTranslation(
                    $landing,
                    $this->requestedLocale($request),
                    false,
                ))
                ->values();

            return LocalServiceLandingSummaryResource::collection($landings);
        }

        $landings = $query
            ->with(['service', 'publicProjects'])
            ->get()
            ->filter(fn (LocalServiceLanding $landing): bool => $this->hasLocaleTranslation(
                $landing,
                $this->requestedLocale($request),
                true,
            ))
            ->values();

        return LocalServiceLandingResource::collection($landings);
    }

    public function show(
        Request $request,
        string $service,
        string $location,
    ): LocalServiceLandingResource|JsonResponse {
        $landing = LocalServiceLanding::query()
            ->publiclyVisible()
            ->with(['service', 'publicProjects'])
            ->where('location_slug', $location)
            ->whereHas(
                'service',
                fn (Builder $query): Builder => $query->where('slug', $service),
            )
            ->first();

        if (! $landing || ! $this->hasLocaleTranslation(
            $landing,
            $this->requestedLocale($request),
            true,
        )) {
            return response()->json(['message' => 'Local service landing not found.'], 404);
        }

        return new LocalServiceLandingResource($landing);
    }

    private function requestedLocale(Request $request): string
    {
        $locale = trim($request->string('locale')->toString());

        if (! in_array($locale, MultilingualContent::LOCALES, true)) {
            $locale = trim((string) $request->header('X-Safetech-Locale', ''));
        }

        return in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
    }

    private function hasLocaleTranslation(
        LocalServiceLanding $landing,
        string $locale,
        bool $requireFullContent,
    ): bool {
        if ($locale === 'ka') {
            return true;
        }

        $fields = [
            'locationName' => $landing->location_name,
            'title' => $landing->title,
            'seoTitle' => $landing->seo_title ?: $landing->title,
        ];

        if ($requireFullContent) {
            $fields['content'] = $landing->content;
            $fields['seoDescription'] = $landing->seo_description ?: ($landing->excerpt ?: $landing->content);
        }

        foreach ($fields as $field => $fallback) {
            $values = MultilingualContent::valuesForField($landing, $field, $fallback);

            if (blank($values[$locale] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
