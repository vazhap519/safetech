<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Support\CategorySeoPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

final class ProductCategoryController extends Controller
{
    public function __invoke(Request $request, CategorySeoPresenter $presenter): JsonResponse
    {
        if (! Schema::hasTable('product_categories') || ! Schema::hasTable('products')) {
            return response()->json(['data' => []]);
        }

        $locale = $request->string('locale')->toString();
        $categories = ProductCategory::query()
            ->whereHas('products', fn ($query) => $query->publiclyVisible())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ProductCategory $category): array => $presenter->present($category, $locale))
            ->values();

        return response()->json(['data' => $categories]);
    }
}
