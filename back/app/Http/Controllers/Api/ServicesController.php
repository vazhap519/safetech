<?php
//
//namespace App\Http\Controllers\Api;
//
//use App\Http\Controllers\Controller;
//use App\Models\Service;
//use App\Models\Settings;
//use Illuminate\Http\Request;
//
//
//
//class ServicesController extends Controller
//{
//    public function index()
//    {
//        $services = Service::all();
//
//        return response()->json([
//            'services' => $services->map(function ($service) {
//                return [
//                    'title' => $service->title,
//                    'description' => $service->description,
//                    'slug' => $service->slug,
//                    'image' => $service->getFirstMediaUrl('services', 'webp')
//                        ? url($service->getFirstMediaUrl('services', 'webp'))
//                        : null,
//
//                    'features' => $service->features ?? [],
//                    'faq' => $service->faq ?? [],
//                    'seo' => $service->seo_text
//                        ? array_filter(array_map('trim', explode("\n", $service->seo_text)))
//                        : [],
//                ];
//            }),
//
//            // 🔥 GLOBAL SHARE
//            'share' => [
//                'title' => settings()->share_title ?? '',
//                'buttons' => settings()->share_buttons ?? [],
//            ],
//        ]);
//    }
//
//    public function show($slug)
//    {
//        $service = Service::where('slug', $slug)->first();
//
//        if (!$service) {
//            return response()->json(['message' => 'Not found'], 404);
//        }
//
//        return response()->json([
//            'service' => [
//                'title' => $service->title,
//                'description' => $service->description,
//                'slug' => $service->slug,
//                'image' => $service->getFirstMediaUrl('services', 'webp'),
//                'features' => $service->features ?? [],
//                'faq' => $service->faq ?? [],
//                'seo' => $service->seo_text ?? null,
//            ],
//
//            // 🔥 GLOBAL SHARE აქაც
//            'share' => [
//                'title' => settings()->share_title ?? '',
//                'buttons' => settings()->share_buttons ?? [],
//            ],
//        ]);
//    }
//}


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceSection;
use App\Models\Settings;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(6);
        $serviceHero = ServiceSection::first();

        return response()->json([
            'services' => $services->getCollection()->map(
                fn($service) => $this->transformService($service)
            ),

            // 🔥 pagination meta
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

            'share' => [
                'title' => settings()->share_title ?? '',
                'buttons' => settings()->share_buttons ?? [],
            ],
            'ServiceHero' => [
                'title' => $serviceHero->service_section_title ?? '',
                'description' => $serviceHero->service_section_description ?? '',
            ]
        ]);
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)->first();

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

    /**
     * 🔥 ცენტრალური transformer (DRY principle)
     */
    private function transformService($service)
    {
        return [
            'title' => $service->title,
            'description' => $service->description,
            'slug' => $service->slug,

            'image' => $service->getFirstMediaUrl('services', 'webp')
                ? url($service->getFirstMediaUrl('services', 'webp'))
                : null,

            // ✅ FIXED (React error აღარ იქნება)
            'features' => collect($service->features)
                ->pluck('text')
                ->filter()
                ->values(),

            // ✅ FAQ სტაბილური სტრუქტურა
            'faq' => collect($service->faq)
                ->map(fn($item) => [
                    'q' => $item['q'] ?? '',
                    'a' => $item['a'] ?? '',
                ])
                ->values(),

            'seo' => $service->seo_text ?? [],

            // ✅ HERO dynamic
            'phone' => $service->phone,
            'button_text' => $service->button_text,
        ];
    }
}
