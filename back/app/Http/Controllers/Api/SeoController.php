<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    /*
     * =====================================
     * GET SEO BY KEY (STATIC)
     * =====================================
     * /api/seo/services
     */
    public function show(string $key): JsonResponse
    {
        $seo = SeoPage::resolve($key);

        return response()->json([
            'success' => true,
            'data' => $seo,
        ]);
    }

    /*
     * =====================================
     * GET SEO FOR MODEL (DYNAMIC)
     * =====================================
     * /api/seo/model/about/1
     */
    public function model(string $type, int $id): JsonResponse
    {
        $class = $this->resolveModelClass($type);

        if (!$class || !class_exists($class)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid model',
            ], 404);
        }

        $model = $class::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model not found',
            ], 404);
        }

        $seo = SeoPage::resolve(model: $model);

        return response()->json([
            'success' => true,
            'data' => $seo,
        ]);
    }

    /*
     * =====================================
     * ALL SEO (LIGHT VERSION)
     * =====================================
     */
    public function index(): JsonResponse
    {
        $pages = SeoPage::query()
            ->select(['key', 'slug', 'title', 'updated_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }

    /*
     * =====================================
     * MODEL MAPPER (SAFE)
     * =====================================
     */
    protected function resolveModelClass(string $type): ?string
    {
        return match ($type) {
            'about' => \App\Models\About::class,
            // 'post' => \App\Models\Post::class,
            // 'service' => \App\Models\Service::class,
            default => null,
        };
    }
}
