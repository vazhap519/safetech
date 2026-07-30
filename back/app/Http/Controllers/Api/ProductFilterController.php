<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ProductRepository;
use App\Http\Controllers\Controller;
use App\Models\ProductFilter;
use App\Support\MultilingualContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

final class ProductFilterController extends Controller
{
    public function __invoke(Request $request, ProductRepository $repository): JsonResponse
    {
        if (! Schema::hasTable('product_filters')) {
            return response()->json(['data' => []]);
        }

        $locale = $this->locale($request);
        $category = $request->string('category')->toString() ?: null;
        $products = $repository->allPublished($category);
        $filters = ProductFilter::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (ProductFilter $filter) use ($locale, $products): ?array {
                $usage = [];

                foreach ($products as $product) {
                    foreach ($product->normalizedFilterValues() as $assignment) {
                        if (($assignment['filter_slug'] ?? null) !== $filter->slug) {
                            continue;
                        }

                        foreach ($assignment['option_slugs'] ?? [] as $optionSlug) {
                            $usage[$optionSlug] = ($usage[$optionSlug] ?? 0) + 1;
                        }
                    }
                }

                $options = $filter->resolvedOptions()
                    ->filter(fn (array $option): bool => isset($usage[$option['slug']]))
                    ->map(function (array $option) use ($locale, $usage): array {
                        $translations = is_array($option['translations'] ?? null)
                            ? $option['translations']
                            : [];
                        $name = is_string($translations[$locale] ?? null) && trim((string) $translations[$locale]) !== ''
                            ? trim((string) $translations[$locale])
                            : $option['label'];

                        return [
                            'slug' => $option['slug'],
                            'name' => $name,
                            'count' => $usage[$option['slug']] ?? 0,
                        ];
                    })
                    ->values()
                    ->all();

                if ($options === []) {
                    return null;
                }

                return [
                    'slug' => $filter->slug,
                    'name' => $this->translated($filter, 'name', $filter->name, $locale),
                    'options' => $options,
                ];
            })
            ->filter()
            ->values();

        return response()->json(['data' => $filters]);
    }

    private function locale(Request $request): string
    {
        $locale = $request->string('locale')->toString();

        return in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
    }

    private function translated(ProductFilter $filter, string $field, mixed $fallback, string $locale): string
    {
        $values = MultilingualContent::valuesForField($filter, $field, $fallback);

        return $values[$locale] ?: (is_string($fallback) ? $fallback : '');
    }
}
