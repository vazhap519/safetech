<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\CategorySeoPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CategoryController extends Controller
{
    public function __invoke(Request $request, CategorySeoPresenter $presenter): JsonResponse
    {
        $locale = $request->string('locale')->toString();
        $categories = Category::query()
            ->whereHas('posts', fn ($query) => $query->where('is_published', true))
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => $presenter->present($category, $locale))
            ->values();

        return response()->json(['data' => $categories]);
    }
}
