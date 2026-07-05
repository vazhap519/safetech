<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProjectController extends Controller
{
    public function index(Request $request, ProjectRepository $repository): AnonymousResourceCollection
    {
        $featured = $request->has('featured') ? $request->boolean('featured') : null;
        return ProjectResource::collection($repository->allPublished($featured));
    }

    public function show(string $slug, ProjectRepository $repository): ProjectResource
    {
        return new ProjectResource($repository->findPublishedBySlug($slug) ?? abort(404));
    }
}
