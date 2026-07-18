<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ServiceRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ServiceController extends Controller
{
    public function index(Request $request, ServiceRepository $repository): AnonymousResourceCollection
    {
        $category = $request->string('category')->toString() ?: null;

        return ServiceResource::collection($repository->allPublished($category));
    }

    public function show(string $slug, ServiceRepository $repository): ServiceResource
    {
        return new ServiceResource($repository->findPublishedBySlug($slug) ?? abort(404));
    }
}
