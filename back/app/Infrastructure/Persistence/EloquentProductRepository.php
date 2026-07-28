<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Contracts\ProductRepository;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class EloquentProductRepository implements ProductRepository
{
    public function allPublished(?string $category = null, array $filters = []): Collection
    {
        if (! $this->productTablesExist()) {
            return collect();
        }

        $products = Product::query()
            ->publiclyVisible()
            ->with(['category', 'media'])
            ->when(
                $category && $category !== 'all',
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('slug', $category),
                ),
            )
            ->get();

        if ($filters === []) {
            return $products;
        }

        return $products
            ->filter(fn (Product $product): bool => $this->matchesFilters($product, $filters))
            ->values();
    }

    public function findPublishedBySlug(string $slug): ?Product
    {
        if (! $this->productTablesExist()) {
            return null;
        }

        return Product::query()
            ->publiclyVisible()
            ->with(['category', 'media'])
            ->where('slug', $slug)
            ->first();
    }

    /** @param array<string, array<int, string>> $filters */
    private function matchesFilters(Product $product, array $filters): bool
    {
        $productFilters = $product->normalizedFilterValues()->keyBy('filter_slug');

        foreach ($filters as $filterSlug => $selectedOptions) {
            $selectedOptions = collect($selectedOptions)
                ->filter(fn (mixed $slug): bool => is_string($slug) && trim($slug) !== '')
                ->map(fn (string $slug): string => trim($slug))
                ->unique()
                ->values()
                ->all();

            if ($selectedOptions === []) {
                continue;
            }

            $assigned = $productFilters->get($filterSlug);

            if (! is_array($assigned)) {
                return false;
            }

            $matches = collect($assigned['option_slugs'] ?? [])
                ->intersect($selectedOptions)
                ->isNotEmpty();

            if (! $matches) {
                return false;
            }
        }

        return true;
    }

    private function productTablesExist(): bool
    {
        return Schema::hasTable('products')
            && Schema::hasTable('product_categories');
    }
}
