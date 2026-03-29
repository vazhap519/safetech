<?php
//
//namespace App\Http\Controllers\Api;
//
//use App\Http\Controllers\Controller;
//use App\Models\About;
//use Illuminate\Support\Facades\Cache;
//
//class AboutController extends Controller
//{
//    public function index()
//    {
//        $about = Cache::remember('about_page', 300, function () {
//            return About::with('media')->first();
//        });
//
//        if (!$about) {
//            return response()->json([
//                'Hero' => null,
//                'Story' => null,
//                'Why' => null,
//                'Cta' => null,
//            ]);
//        }
//
//        return response()->json([
//            'Hero' => [
//                'title' => $about->hero_title,
//                'description' => $about->hero_description,
//                'trust_list' => $about->hero_trust_list ?? [],
//                'badge' => $about->hero_badge,
//                'image' => $about->image_url,
//            ],
//
//            'Story' => [
//                'title' => $about->story_title,
//                'description' => $about->story_title_description,
//                'content' => $about->story_content,
//                'stats' => $about->story_stats ?? [],
//            ],
//
//            'Why' => [
//                'title' => $about->why_us_title,
//                'description' => $about->why_us_title_description,
//                'items' => $about->why_us_content ?? [],
//            ],
//
//            'Cta' => [
//                'title' => $about->cta_title,
//                'description' => $about->cta_title_description,
//                'trust' => $about->cta_trust ?? [],
//                'phone' => $about->cta_phone,
//            ],
//        ]);
//    }
//}


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AboutController extends Controller
{
    public function index()
    {
        $about = Cache::remember('about_page', 300, function () {
            return About::with('media')->first();
        });

        // ✅ SEO წამოღება
        $seo = \App\Models\SeoPage::getByKey('about');

        if (!$about) {
            return response()->json([
                'data' => [
                    'Hero' => null,
                    'Story' => null,
                    'Why' => null,
                    'Cta' => null,
                ],
                'seo' => [
                    'meta' => $seo?->meta ?? [],
                    'schema' => $seo?->schema_data ?? [],
                ],
            ]);
        }

        return response()->json([

            // 🔥 მთავარი FIX (ყველა გვერდის სტანდარტი)
            'data' => [
                'Hero' => [
                    'title' => $about->hero_title,
                    'description' => $about->hero_description,
                    'trust_list' => $about->hero_trust_list ?? [],
                    'badge' => $about->hero_badge,
                    'image' => $about->image_url,
                ],

                'Story' => [
                    'title' => $about->story_title,
                    'description' => $about->story_title_description,
                    'content' => $about->story_content,
                    'stats' => $about->story_stats ?? [],
                ],

                'Why' => [
                    'title' => $about->why_us_title,
                    'description' => $about->why_us_title_description,
                    'items' => $about->why_us_content ?? [],
                ],

                'Cta' => [
                    'title' => $about->cta_title,
                    'description' => $about->cta_title_description,
                    'trust' => $about->cta_trust ?? [],
                    'phone' => $about->cta_phone,
                ],
            ],

            // 🔥 SEO BLOCK (ძალიან მნიშვნელოვანი)
            'seo' => [
                'meta' => $seo?->meta ?? [],
                'schema' => $seo?->schema_data ?? [],
            ],
        ]);
    }

    /**
     * 🔥 UPDATE (მთავარი ნაწილი)
     */
    public function update(Request $request)
    {
        $about = About::first();

        if (!$about) {
            $about = About::create($request->all());
        } else {
            $about->update($request->all());
        }

        // ✅ 1. Laravel cache წაშლა
        Cache::forget('about_page');

        // ✅ 2. Next.js revalidate
        Http::post('http://localhost:3000/api/revalidate', [
            'tag' => 'about'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'About updated successfully'
        ]);
    }
}
