<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ServicesController extends Controller
{
    /**
     * LIST (cached + optimized)
     */
    public function index()
    {
        return Cache::remember('services_list', 300, function () {

            $services = Service::with('media')
                ->select(
                    'id',
                    'title',
                    'description',
                    'slug',
                    'features',
                    'faq',
                    'seo_text',
                    'phone',
                    'button_text'
                )
                ->latest()
                ->paginate(6);

            $serviceHero = ServiceSection::first();

            return response()->json([
                'services' => $services->getCollection()->map(
                    fn($service) => $this->transformService($service)
                ),

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

                'serviceHero' => $serviceHero ? [
                    'title' => $serviceHero->service_section_title,
                    'description' => $serviceHero->service_section_description,
                    'image' => $serviceHero->image_url ?? null,
                ] : null,
            ]);
        });
    }
    public function revalidate()
    {
        // ✅ 1. ყველა services cache წაშლა
        Cache::forget('services_list');

        // თუ გინდა ყველა service cache წაშალო:
        // Cache::flush(); ❌ (არ გირჩევ)

        // ✅ 2. Next.js revalidate (list)
        Http::post('http://localhost:3000/api/revalidate', [
            'tag' => 'services'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Services cache cleared'
        ]);
    }
    /**
     * SHOW (🔥 critical performance fix)
     */
    public function show($slug)
    {
        $service = Cache::remember("service_{$slug}", 300, function () use ($slug) {
            return Service::with('media')->where('slug', $slug)->first();
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
    public function revalidateSingle($slug)
    {
        Cache::forget("service_{$slug}");

        Http::post('http://localhost:3000/api/revalidate', [
            'tag' => "service-{$slug}"
        ]);

        return response()->json([
            'success' => true,
            'message' => "Service {$slug} revalidated"
        ]);
    }
    /**
     * 🔥 CLEAN TRANSFORMER (final form)
     */
    private function transformService($service)
    {
        return [
            'title' => $service->title,
            'description' => $service->description,
            'slug' => $service->slug,

            // ✅ accessor გამოყენება (no duplication)
            'image' => $service->image_url,

            // ✅ ALWAYS ARRAY
            'features' => collect($service->features)
                ->pluck('text')
                ->filter()
                ->values(),

            'faq' => collect($service->faq)
                ->map(fn($item) => [
                    'q' => $item['q'] ?? '',
                    'a' => $item['a'] ?? '',
                ])
                ->values(),

            // ✅ NEVER breaks frontend
            'seo' => $service->seo,

            'phone' => $service->phone,
            'button_text' => $service->button_text,
        ];
    }
}
