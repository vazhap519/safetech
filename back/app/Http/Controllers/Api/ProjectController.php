<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProjectController extends Controller
{
    public function index(Request $request, ProjectRepository $repository): AnonymousResourceCollection
    {
        $featured = $request->has('featured') ? $request->boolean('featured') : null;
        $category = $request->string('category')->toString() ?: null;

        return ProjectResource::collection($repository->allPublished($featured, $category));
    }

    public function show(string $slug, ProjectRepository $repository): ProjectResource|JsonResponse
    {
        $project = $repository->findPublishedBySlug($slug);

        if (! $project) {
            return response()->json([
                'message' => 'Project not found.',
            ], 404);
        }

        return new ProjectResource($project);
    }
}
