<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectSummaryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProjectController extends Controller
{
    public function index(Request $request, ProjectRepository $repository): AnonymousResourceCollection
    {
        $featured = $request->has('featured') ? $request->boolean('featured') : null;
        $category = $request->string('category')->toString() ?: null;
        $projects = $repository->allPublished($featured, $category);

        if ($request->string('view')->toString() === 'summary') {
            return ProjectSummaryResource::collection($projects);
        }

        return ProjectResource::collection($projects);
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
