<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ServiceRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ServiceController extends Controller
{
    public function index(ServiceRepository $repository): AnonymousResourceCollection
    {
        return ServiceResource::collection($repository->allPublished());
    }

    public function show(string $slug, ServiceRepository $repository): ServiceResource
    {
        return new ServiceResource($repository->findPublishedBySlug($slug) ?? abort(404));
    }
}
