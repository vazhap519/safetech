<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PrivacyController extends Controller
{
    /**
     * GET
     */
    public function index()
    {
        $privacy = Cache::remember('privacy_page', 300, function () {
            return PrivacyPolicy::first();
        });

        // ✅ SEO წამოღება
        $seo = \App\Models\SeoPage::getByKey('privacy');

        return response()->json([

            // 🔥 unified data (ყველა გვერდის მსგავსად)
            'data' => [
                'title' => $privacy->title ?? '',
                'highlight' => $privacy->highlight ?? '',
                'content' => $privacy->content ?? '',
            ],

            // 🔥 SEO BLOCK (ძალიან მნიშვნელოვანი)
            'seo' => [
                'meta' => $seo?->meta ?? [],
                'schema' => $seo?->schema_data ?? [],
            ],
        ]);
    }

    /**
     * 🔥 UPDATE + REVALIDATE
     */
    public function update(Request $request)
    {
        $privacy = PrivacyPolicy::first();

        if (!$privacy) {
            $privacy = PrivacyPolicy::create($request->all());
        } else {
            $privacy->update($request->all());
        }

        // ✅ 1. Laravel cache clear
        Cache::forget('privacy_page');

        // ✅ 2. Next.js revalidate
        Http::post('http://localhost:3000/api/revalidate', [
            'tag' => 'privacy'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Privacy updated'
        ]);
    }
}
