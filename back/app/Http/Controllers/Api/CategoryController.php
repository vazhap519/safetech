<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\CategorySeoPresenter;
use App\Support\PublicContentEligibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CategoryController extends Controller
{
    public function __invoke(Request $request, CategorySeoPresenter $presenter): JsonResponse
    {
        $locale = $request->string('locale')->toString();
        $categories = Category::query()
            ->whereHas('posts', fn ($query) => $query->publiclyVisible())
            ->with(['posts' => fn ($query) => $query
                ->publiclyVisible()
                ->with('sections')])
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $category): bool => $category->posts->contains(
                fn ($post): bool => PublicContentEligibility::post($post, $locale),
            ))
            ->map(fn (Category $category): array => $presenter->present($category, $locale))
            ->values();

        return response()->json(['data' => $categories]);
    }
}
