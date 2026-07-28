<?php

namespace App\Http\Controllers\Api;

use App\Domain\Content\Contracts\ProductRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProductController extends Controller
{
    public function index(Request $request, ProductRepository $repository): AnonymousResourceCollection
    {
        $category = $request->string('category')->toString() ?: null;

        return ProductResource::collection(
            $repository->allPublished($category, $this->filters($request)),
        );
    }

    public function show(string $slug, ProductRepository $repository): ProductResource|JsonResponse
    {
        $product = $repository->findPublishedBySlug($slug);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        return new ProductResource($product);
    }

    /** @return array<string, array<int, string>> */
    private function filters(Request $request): array
    {
        return collect($request->query())
            ->filter(fn (mixed $value, string $key): bool => str_starts_with($key, 'filter_'))
            ->mapWithKeys(function (mixed $value, string $key): array {
                $slug = substr($key, 7);
                $values = collect(is_array($value) ? $value : explode(',', (string) $value))
                    ->filter(fn (mixed $item): bool => is_string($item) && trim($item) !== '')
                    ->map(fn (string $item): string => trim($item))
                    ->values()
                    ->all();

                return $slug !== '' && $values !== [] ? [$slug => $values] : [];
            })
            ->all();
    }
}
