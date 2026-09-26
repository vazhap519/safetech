<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ServiceRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCardResource;
use App\Http\Resources\ServiceOptionResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceSitemapResource;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ServiceController extends Controller
{
    public function index(Request $request, ServiceRepository $repository): AnonymousResourceCollection
    {
        $category = $request->string('category')->toString() ?: null;
        $view = $request->string('view')->toString();

        if ($view === 'card') {
            $services = $this->publicServices($category)
                ->with(['category:id,name,slug,translations'])
                ->get([
                    'id',
                    'category_for_service_id',
                    'slug',
                    'name',
                    'title',
                    'description',
                    'short_description',
                    'long_description',
                    'icon',
                    'translations',
                    'sort_order',
                ]);

            return ServiceCardResource::collection($services);
        }

        if ($view === 'sitemap') {
            $services = $this->publicServices($category)
                ->with(['category:id,slug', 'media'])
                ->get([
                    'id',
                    'category_for_service_id',
                    'slug',
                    'name',
                    'title',
                    'seo',
                    'hero_image',
                    'updated_at',
                    'sort_order',
                ]);

            return ServiceSitemapResource::collection($services);
        }

        return ServiceResource::collection($repository->allPublished($category));
    }

    public function options(): AnonymousResourceCollection
    {
        $services = Service::query()
            ->publiclyVisible()
            ->get(['id', 'slug', 'name', 'title', 'translations', 'sort_order']);

        return ServiceOptionResource::collection($services);
    }

    public function show(string $slug, ServiceRepository $repository): ServiceResource|JsonResponse
    {
        $service = $repository->findPublishedBySlug($slug);

        if (! $service) {
            return response()->json([
                'message' => 'Service not found.',
            ], 404);
        }

        return new ServiceResource($service);
    }

    /** @return Builder<Service> */
    private function publicServices(?string $category): Builder
    {
        return Service::query()
            ->publiclyVisible()
            ->when(
                $category && $category !== 'all',
                fn (Builder $query): Builder => $query->whereHas(
                    'category',
                    fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $category),
                ),
            );
    }
}
