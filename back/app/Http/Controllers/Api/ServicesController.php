<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\ServiceSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ServicesController extends Controller
{

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $category = $request->get('category');

        // 🔥 cache key უნდა შეიცვალოს
        $cacheKey = "services_list_page_{$page}_cat_" . ($category ?? 'all');

        $data = Cache::remember($cacheKey, 30, function () use ($category) {

            $query = Service::query()
                ->select([
                    'id',
                    'title',
                    'short_description',
                    'slug',
                    'category_for_service_id',
                ])
                ->with(['media', 'category'])
                ->latest();

            // 🔥 FILTER
            if ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            $services = $query->paginate(6)->appends([
                'category' => $category
            ]);
            $serviceHero = ServiceSection::first();

            return [
                'services' => $services->getCollection()->map(fn($service) => [
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'short_description' => $service->short_description,
                    'image' => $service->image,

                    // 🔥 NEW
                    'category' => [
                        'name' => $service->category?->name,
                        'slug' => $service->category?->slug,
                    ],
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
                    'description' => $serviceHero->service_section_description,
                    'image' => $serviceHero->image_url ?? null,
                ] : null,
            ];
        });

        $seo = SeoPage::getByKey('services');

        return response()->json([
            'success' => true,
            'data' => $data,
            'seo' => [
                'meta' => $seo?->meta ?? [],
                'schema' => $seo?->schema_data ?? [],
            ],
            'share' => [
                'title' => settings()->share_title ?? '',
                'buttons' => settings()->share_buttons ?? [],
            ],
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | 🔥 SINGLE
    |--------------------------------------------------------------------------
    */
    public function show($slug)
    {
        $service = Cache::remember("service_{$slug}", 30, function () use ($slug) {
            return Service::query()
                ->select([
                    'id',
                    'slug',
                    'title',
                    'short_description',
                    'long_description',
                    'features',
                    'faq',
                    'phone',
                    'button_text',
                    'cta_title',
                    'cta_description',
                ])
                ->with('media')
                ->where('slug', $slug)
                ->first();
        });

        if (!$service) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // 🔥 SHARE BUILD (clean + dynamic)
        $url = url("/services/{$service->slug}");

        $shareButtons = collect($this->shareMap())
            ->map(function ($btn) use ($url) {
                return [
                    'name' => $btn['name'],
                    'icon' => $btn['icon'],
                    'color' => $btn['color'],
                    'url' => str_replace('{url}', urlencode($url), $btn['url']),
                ];
            })
            ->values();

        return response()->json([
            'service' => [
                'title' => $service->title,
                'short_description' => $service->short_description,
                'long_description' => $service->long_description,

                'image' => $service->image,

                'features' => $service->features ?? [],
                'faq' => $service->faq ?? [],

                'phone' => $service->phone,
                'button_text' => $service->button_text,

                'cta_title' => $service->cta_title,
                'cta_description' => $service->cta_description,
            ],

            // ✅ CLEAN SHARE OUTPUT
            'share' => [
                'url' => $url,
                'buttons' => $shareButtons,
            ]
        ]);
    }
    private function shareMap()
    {
        return [
            'facebook' => [
                'name' => 'Facebook',
                'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                'color' => 'bg-blue-600',
                'icon' => 'FaFacebook',
            ],
            'whatsapp' => [
                'name' => 'WhatsApp',
                'url' => 'https://wa.me/?text={url}',
                'color' => 'bg-green-500',
                'icon' => 'FaWhatsapp',
            ],
            'telegram' => [
                'name' => 'Telegram',
                'url' => 'https://t.me/share/url?url={url}',
                'color' => 'bg-sky-500',
                'icon' => 'FaTelegram',
            ],
            'linkedin' => [
                'name' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                'color' => 'bg-blue-700',
                'icon' => 'FaLinkedin',
            ],
            'pinterest' => [
                'name' => 'Pinterest',
                'url' => 'https://pinterest.com/pin/create/button/?url={url}',
                'color' => 'bg-red-600',
                'icon' => 'FaPinterest',
            ],
            'twitter' => [
                'name' => 'Twitter',
                'url' => 'https://twitter.com/intent/tweet?url={url}',
                'color' => 'bg-black',
                'icon' => 'FaTwitter',
            ],
            'link' => [
                'name' => 'Copy Link',
                'url' => '{url}',
                'color' => 'bg-gray-600',
                'icon' => 'FaLink',
            ],
        ];
    }

    public function revalidate()
    {
        Cache::flush();
        $this->notifyFrontendRevalidate('services');

        return response()->json([
            'success' => true,
            'message' => 'Services cache cleared',
        ]);
    }

    public function revalidateSingle(string $slug)
    {
        Cache::forget("service_{$slug}");
        $this->notifyFrontendRevalidate("service-{$slug}");

        return response()->json([
            'success' => true,
            'message' => 'Service cache cleared',
        ]);
    }

    private function notifyFrontendRevalidate(string $tag): void
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', '')), '/');

        if (!$frontendUrl) {
            return;
        }

        try {
            Http::timeout(3)->post("{$frontendUrl}/api/revalidate", [
                'tag' => $tag,
            ]);
        } catch (\Throwable) {
            //
        }
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
            'slug' => $service->slug,

            'short_description' => $service->short_description,
            'long_description' => $service->long_description,

            'image' => $service->image,
            /*
            |--------------------------------------------------
            | ✅ ARRAYS
            |--------------------------------------------------
            */
            'problems' => collect($service->problems ?? [])
                ->map(fn($item) => [
                    'text' => $item['text'] ?? $item,
                ])
                ->values(),

            'features' => collect($service->features ?? [])
                ->map(fn($item) => [
                    'text' => $item['text'] ?? $item,
                ])
                ->values(),

            'results' => collect($service->results ?? [])
                ->map(fn($item) => [
                    'text' => $item['text'] ?? $item,
                ])
                ->values(),

            'testimonials' => collect($service->testimonials ?? [])
                ->map(fn($item) => [
                    'name' => $item['name'] ?? 'Client',
                    'text' => $item['text'] ?? '',
                ])
                ->values(),

            'case_study' => [
                'title' => $service->case_study['title'] ?? null,
                'description' => $service->case_study['description'] ?? null,
                'result' => $service->case_study['result'] ?? null,
            ],

            /*
            |--------------------------------------------------
            | ❓ FAQ (UI 그대로 ვტოვებთ)
            |--------------------------------------------------
            */
            'faq' => collect($service->faq ?? [])
                ->map(fn($item) => [
                    'q' => $item['q'] ?? '',
                    'a' => $item['a'] ?? '',
                ])
                ->values(),

            /*
            |--------------------------------------------------
            | 🎯 CTA
            |--------------------------------------------------
            */
            'cta' => [
                'title' => $service->cta_title ?? null,
                'description' => $service->cta_description ?? null,
                'phone' => $service->phone ?? null,
                'button_text' => $service->button_text ?? null,
            ],

            /*
            |--------------------------------------------------
            | 🔥 SEO (ეს არის ერთადერთი კრიტიკული FIX)
            |--------------------------------------------------
            */
            'seo' => [
                'meta' => [
                    'title' => $service->seo['title'] ?? $service->title,

                    'description' =>
                        $service->seo['description']
                        ?? $service->short_description
                            ?? $service->long_description,

                    'keywords' => collect($service->seo['keywords'] ?? [])
                        ->pluck('value')
                        ->filter()
                        ->values(),

                    'image' => $service->image,
                ],

                // FAQ schema-სთვის (frontend-ში გამოიყენებ)
                'faq' => collect($service->faq ?? [])
                    ->map(fn($item) => [
                        'question' => $item['q'] ?? '',
                        'answer' => $item['a'] ?? '',
                    ])
                    ->values(),

                // future use (არ ვეხებით)
                'schema' => $service->seo['schema'] ?? null,
            ],
        ];
    }


}
