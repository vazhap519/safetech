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
    /**
     * 🔵 LIST (FIXED CACHE + PAGINATION)
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);

        $cacheKey = "services_list_page_{$page}";

        return Cache::remember($cacheKey, 300, function () {

            $services = Service::query()
                ->select([
                    'id',
                    'title',
                    'description',
                    'slug',
                    'features',
                    'faq',
                    'seo_text',
                    'phone',
                    'button_text'
                ])
                ->with('media:id,model_id,collection_name,file_name')
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

    /**
     * 🔥 SINGLE
     */
    public function show($slug)
    {
        $cacheKey = "service_{$slug}";

        $service = Cache::remember($cacheKey, 300, function () use ($slug) {
            return Service::query()
                ->with('media:id,model_id,collection_name,file_name')
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

    /**
     * 🔄 REVALIDATE LIST
     */
    public function revalidate()
    {
        // 🔥 clear all service list cache (pages)
        Cache::flush(); // თუ გინდა granular, მერე დავწერთ

        try {
            Http::withHeaders([
                'x-secret' => config('services.next.secret'),
            ])->post(config('services.next.revalidate_url'), [
                'tag' => 'services',
                'path' => '/services',
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * 🔄 REVALIDATE SINGLE
     */
    public function revalidateSingle($slug)
    {
        Cache::forget("service_{$slug}");

        try {
            Http::withHeaders([
                'x-secret' => config('services.next.secret'),
            ])->post(config('services.next.revalidate_url'), [
                'tag' => "service-{$slug}",
                'path' => "/services/{$slug}",
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * 🔥 TRANSFORMER (FIXED)
     */
    private function transformService($service)
    {
        return [
            'title' => $service->title,
            'description' => $service->description,
            'slug' => $service->slug,

            'image' => $service->image_url,

            // 🔥 FIX: simple array
            'features' => collect($service->features)
                ->filter()
                ->values(),

            'faq' => collect($service->faq)
                ->map(fn($item) => [
                    'q' => $item['q'] ?? '',
                    'a' => $item['a'] ?? '',
                ])
                ->values(),

            'seo' => $service->seo,

            'phone' => $service->phone,
            'button_text' => $service->button_text,
        ];
    }
}
