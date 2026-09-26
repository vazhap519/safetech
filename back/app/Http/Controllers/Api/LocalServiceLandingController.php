<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocalServiceLandingResource;
use App\Http\Resources\LocalServiceLandingSitemapResource;
use App\Http\Resources\LocalServiceLandingSummaryResource;
use App\Models\LocalServiceLanding;
use Illuminate\Database\Eloquent\Builder;
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
                    'publicProjects' => fn (Builder $projects): Builder => $projects
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
                ]);

            return LocalServiceLandingSummaryResource::collection($landings);
        }

        return LocalServiceLandingResource::collection(
            $query->with(['service', 'publicProjects'])->get(),
        );
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

        if (! $landing) {
            return response()->json(['message' => 'Local service landing not found.'], 404);
        }

        return new LocalServiceLandingResource($landing);
    }
}
