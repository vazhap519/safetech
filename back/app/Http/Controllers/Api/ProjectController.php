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

        $perPage = min(max($request->integer('per_page', 0), 0), 24);
        $page = max($request->integer('page', 1), 1);

        if ($perPage > 0) {
            $total = $projects->count();
            $lastPage = max((int) ceil($total / $perPage), 1);
            $page = min($page, $lastPage);
            $projects = $projects->slice(($page - 1) * $perPage, $perPage)->values();
        }

        if ($request->string('view')->toString() === 'summary') {
            $collection = ProjectSummaryResource::collection($projects);

            return $perPage > 0
                ? $collection->additional(['meta' => [
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                ]])
                : $collection;
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
