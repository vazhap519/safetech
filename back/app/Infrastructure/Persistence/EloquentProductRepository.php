<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Contracts\ProductRepository;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class EloquentProductRepository implements ProductRepository
{
    public function allPublished(?string $category = null, array $filters = []): Collection
    {
        if (! $this->productTablesExist()) {
            return collect();
        }

        $normalizedFilters = $this->normalizeFilters($filters);

        $query = Product::query()
            ->publiclyVisible()
            ->with(['category', 'media'])
            ->when(
                $category && $category !== 'all',
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('slug', $category),
                ),
            );

        if ($normalizedFilters !== []) {
            $this->applyQueryFilters($query, $normalizedFilters);
        }

        $products = $query->get();

        if ($normalizedFilters === []) {
            return $products;
        }

        return $products
            ->filter(fn (Product $product): bool => $this->matchesFilters($product, $normalizedFilters))
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

    /** @param array<string, array<int, string>> $filters */
    private function applyQueryFilters(Builder $query, array $filters): void
    {
        $driver = $query->getConnection()->getDriverName();

        foreach ($filters as $filterSlug => $selectedOptions) {
            if ($selectedOptions === []) {
                continue;
            }

            match ($driver) {
                'sqlite' => $this->applySqliteFilter($query, $filterSlug, $selectedOptions),
                'mysql' => $this->applyMysqlFilter($query, $filterSlug, $selectedOptions),
                default => null,
            };
        }
    }

    /** @param array<int, string> $selectedOptions */
    private function applySqliteFilter(Builder $query, string $filterSlug, array $selectedOptions): void
    {
        $placeholders = implode(', ', array_fill(0, count($selectedOptions), '?'));
        $table = $query->getModel()->getTable();

        $query->whereRaw(
            <<<SQL
            exists (
                select 1
                from json_each({$table}.filter_values) as filter_item
                where json_extract(filter_item.value, '$.filter_slug') = ?
                  and exists (
                      select 1
                      from json_each(json_extract(filter_item.value, '$.option_slugs')) as option_item
                      where option_item.value in ({$placeholders})
                  )
            )
            SQL,
            array_merge([$filterSlug], $selectedOptions),
        );
    }

    /** @param array<int, string> $selectedOptions */
    private function applyMysqlFilter(Builder $query, string $filterSlug, array $selectedOptions): void
    {
        $table = $query->getModel()->getTable();
        $filterPathSql = "replace(json_unquote(json_search({$table}.filter_values, 'one', ?, null, '$[*].filter_slug')), '.filter_slug', '.option_slugs')";
        $optionSearchSql = "json_search(json_extract({$table}.filter_values, {$filterPathSql}), 'one', ?, null, '$[*]') is not null";

        $query->where(function (Builder $nestedQuery) use ($filterSlug, $selectedOptions, $optionSearchSql): void {
            foreach ($selectedOptions as $index => $optionSlug) {
                $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';

                $nestedQuery->{$method}($optionSearchSql, [$filterSlug, $optionSlug]);
            }
        });
    }

    /**
     * @param  array<string, array<int, string>>  $filters
     * @return array<string, array<int, string>>
     */
    private function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->mapWithKeys(function (mixed $selectedOptions, mixed $filterSlug): array {
                if (! is_string($filterSlug) || trim($filterSlug) === '') {
                    return [];
                }

                $normalizedOptions = collect(is_array($selectedOptions) ? $selectedOptions : [$selectedOptions])
                    ->filter(fn (mixed $slug): bool => is_string($slug) && trim($slug) !== '')
                    ->map(fn (string $slug): string => trim($slug))
                    ->unique()
                    ->values()
                    ->all();

                return $normalizedOptions === []
                    ? []
                    : [trim($filterSlug) => $normalizedOptions];
            })
            ->all();
    }

    private function productTablesExist(): bool
    {
        return Schema::hasTable('products')
            && Schema::hasTable('product_categories');
    }
}
