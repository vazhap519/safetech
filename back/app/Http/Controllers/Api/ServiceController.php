<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ServiceRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCardResource;
use App\Http\Resources\ServiceOptionResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ServiceController extends Controller
{
    public function index(Request $request, ServiceRepository $repository): AnonymousResourceCollection
    {
        $category = $request->string('category')->toString() ?: null;
        $services = $repository->allPublished($category);

        if ($request->string('view')->toString() === 'card') {
            return ServiceCardResource::collection($services);
        }

        return ServiceResource::collection($services);
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
}
