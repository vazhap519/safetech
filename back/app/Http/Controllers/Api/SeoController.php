<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Support\MultilingualContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /*
     * =====================================
     * GET SEO BY KEY (STATIC)
     * =====================================
     * /api/seo/services
     */
    public function show(Request $request, string $key): JsonResponse
    {
        $locale = $request->string('locale')->toString();
        $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
        $seo = SeoPage::getByKey($key);

        if (! $seo) {
            return response()->json([
                'success' => false,
                'message' => 'SEO page not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $seo->localizedMeta($locale),
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
            ->select(['key', 'slug', 'title', 'noindex', 'updated_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }
}
