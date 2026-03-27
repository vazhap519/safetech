<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ServicesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 🔵 LIST
    |--------------------------------------------------------------------------
    */
//    public function index(Request $request)
//    {
//        $page = $request->get('page', 1);
//        $cacheKey = "services_list_page_{$page}";
//
//        return Cache::remember($cacheKey, 300, function () {
//
//            $services = Service::query()
//                ->select([
//                    'id',
//                    'title',
//                    'short_description',
//                    'slug',
//                    'features',
//                    'faq',
//                    'seo',
//                    'phone',
//                    'button_text'
//                ])
//                ->with('media') // ✅ FIX (ძალიან მნიშვნელოვანი)
//                ->latest()
//                ->paginate(6);
//
//            $serviceHero = ServiceSection::first();
//
//            return response()->json([
//                'services' => $services->getCollection()->map(
//                    fn($service) => $this->transformService($service)
//                ),
//
//                'meta' => [
//                    'current_page' => $services->currentPage(),
//                    'last_page' => $services->lastPage(),
//                    'per_page' => $services->perPage(),
//                    'total' => $services->total(),
//                ],
//
//                'links' => [
//                    'next' => $services->nextPageUrl(),
//                    'prev' => $services->previousPageUrl(),
//                ],
//
//                'share' => [
//                    'title' => settings()->share_title ?? '',
//                    'buttons' => settings()->share_buttons ?? [],
//                ],
//
//                'serviceHero' => $serviceHero ? [
//                    'title' => $serviceHero->service_section_title,
//                    'hero_description' => $serviceHero->service_section_description,
//                    'image' => $serviceHero->image_url ?? null,
//                ] : null,
//            ]);
//        });
//    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $cacheKey = "services_list_page_{$page}";

        return Cache::remember($cacheKey, 300, function () {

            $services = Service::query()
                ->select([
                    'id',
                    'title',
                    'short_description', // ✅ მხოლოდ ეს გვჭირდება
                    'slug',
                ])
                ->with('media')
                ->latest()
                ->paginate(6);

            $serviceHero = ServiceSection::first();

            return response()->json([
                'success' => true,

                'data' => [
                    'services' => $services->getCollection()->map(fn($service) => [
                        'title' => $service->title,
                        'slug' => $service->slug,

                        // 🔥 LIST-ში მხოლოდ მოკლე ტექსტი
                        'short_description' => $service->short_description,

                        'image' => $service->image_url,
                    ]),

                    'meta' => [
                        'current_page' => $services->currentPage(),
                        'last_page' => $services->lastPage(),
                        'per_page' => $services->perPage(),
                        'total' => $services->total(),
                    ],

                    'links' => [
                        'next' => $services->nextPageUrl(),
                        'prev' => $services->previousPageUrl(),
                    ],

                    'serviceHero' => $serviceHero ? [
                        'title' => $serviceHero->service_section_title,

                        // 🔥 FIX (მთავარი)
                        'description' => $serviceHero->service_section_description,

                        'image' => $serviceHero->image_url ?? null,
                    ] : null,
                ],

                'share' => [
                    'title' => settings()->share_title ?? '',
                    'buttons' => settings()->share_buttons ?? [],
                ],
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 SINGLE
    |--------------------------------------------------------------------------
    */
    public function show($slug)
    {
        $cacheKey = "service_{$slug}";

        $service = Cache::remember($cacheKey, 300, function () use ($slug) {
            return Service::query()
                ->with('media') // ✅ FIX
                ->where('slug', $slug)
                ->first();
        });

        if (!$service) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'service' => $this->transformService($service),

            'share' => [
                'title' => settings()->share_title ?? '',
                'buttons' => settings()->share_buttons ?? [],
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 TRANSFORMER
    |--------------------------------------------------------------------------
    */
    private function transformService($service)
    {
        return [
            'title' => $service->title,
            'long_description' => $service->long_description,
            'short_description' => $service->short_description,
            'slug' => $service->slug,

            'image' => $service->image_url,

            'features' => collect($service->features)
                ->filter()
                ->values(),

            'faq' => collect($service->faq)
                ->map(fn($item) => [
                    'q' => $item['q'] ?? '',
                    'a' => $item['a'] ?? '',
                ])
                ->values(),

            'seo' => $service->seo?? [],

            'phone' => $service->phone,
            'button_text' => $service->button_text,
        ];
    }
}
