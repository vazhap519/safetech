<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Support\CategorySeoPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProjectCategoryController extends Controller
{
    public function __invoke(Request $request, CategorySeoPresenter $presenter): JsonResponse
    {
        $locale = $request->string('locale')->toString();
        $categories = ProjectCategory::query()
            ->whereHas('projects', fn ($query) => $query->where('is_published', true))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ProjectCategory $category): array => $presenter->present($category, $locale))
            ->values();

        return response()->json(['data' => $categories]);
    }
}
