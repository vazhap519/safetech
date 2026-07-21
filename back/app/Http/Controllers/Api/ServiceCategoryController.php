<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryForService;
use App\Support\CategorySeoPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ServiceCategoryController extends Controller
{
    public function __invoke(Request $request, CategorySeoPresenter $presenter): JsonResponse
    {
        $locale = $request->string('locale')->toString();
        $categories = CategoryForService::query()
            ->whereHas('services', fn ($query) => $query->publiclyVisible())
            ->orderBy('name')
            ->get()
            ->map(fn (CategoryForService $category): array => $presenter->present($category, $locale))
            ->values();

        return response()->json(['data' => $categories]);
    }
}
