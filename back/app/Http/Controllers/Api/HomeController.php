<?php
//
//namespace App\Http\Controllers\Api;
//
//use App\Http\Controllers\Controller;
//use App\Models\HomeStat;
//use App\Models\HomeTestimonial;
//use App\Models\HomeTrust;
//use App\Models\ServiceSection;
//use App\Models\HomeHeroSection;
//use App\Models\HomeWhyUs;
//use App\Models\HomeHowWork;
//use App\Models\HomeCtaSection;
//use App\Models\FaqSection;
//use App\Models\Service;
//use Illuminate\Support\Facades\Cache;
//use Illuminate\Support\Facades\Http;
//use App\Models\SeoPage;
//class HomeController extends Controller
//{
//    /**
//     * GET HOME DATA
//     */
//    public function index()
//    {
//        $data = Cache::remember('home_api', 300, function () {
//
//            $hero = HomeHeroSection::first();
//            $why = HomeWhyUs::first();
//            $how = HomeHowWork::first();
//            $cta = HomeCtaSection::first();
//            $faq = FaqSection::first();
//            $services = Service::latest()->take(6)->get();
//            $serviceSection = ServiceSection::first();
//
//            // 🔥 NEW SECTIONS (FIXED)
//            $trust = HomeTrust::where('is_active', true)->latest()->first();
//            $stats = HomeStat::where('is_active', true)->latest()->first();
//            $testimonials = HomeTestimonial::where('is_active', true)->latest()->first();
//            $seo = SeoPage::getByKey('home');
//            return [
//
//                /* =========================
//                   HERO
//                ========================== */
//                'homeHero' => $hero ? [
//                    'title' => $hero->home_hero_title,
//                    'description' => $hero->home_hero_description,
//                    'list' => $hero->home_hero_list,
//                    'call_button' => $hero->home_hero_call_button_text,
//                    'call_number' => $hero->home_hero_call_button_number,
//                    'service_button' => $hero->home_hero_service_button_text,
//                    'image' => $hero->image_url,
//                ] : null,
//
//                /* =========================
//                   WHY US
//                ========================== */
//                'whyUs' => $why ? [
//                    'title' => $why->why_us_title,
//                    'description' => $why->why_us_description,
//                    'items' => $why->why_us_items ?? [],
//                ] : null,
//
//                /* =========================
//                   HOW IT WORKS
//                ========================== */
//                'howWork' => $how ? [
//                    'title' => $how->title,
//                    'description' => $how->description,
//                    'cards' => is_string($how->how_cards)
//                        ? json_decode($how->how_cards, true)
//                        : ($how->how_cards ?? []),
//                ] : null,
//
//                /* =========================
//                   CTA
//                ========================== */
//                'Cta' => $cta ? [
//                    'cta_title' => $cta->cta_title,
//                    'cta_title_hilight' => $cta->cta_title_hilight,
//                    'cta_description' => $cta->cta_description,
//                    'cta_phone_btn_number' => $cta->cta_phone_btn_number,
//                    'cta_phone_btn_text' => $cta->cta_phone_btn_text,
//                    'cta_message_button_text' => $cta->cta_message_button_text,
//                ] : null,
//
//                /* =========================
//                   FAQ
//                ========================== */
//                'Faq' => $faq ? [
//                    'title' => $faq->title,
//                    'description' => $faq->description,
//                    'faq' => $faq->faq ?? [],
//                ] : null,
//
//                /* =========================
//                   🔥 NEW: TRUST (LOGOS)
//                ========================== */
//                'trust' => $trust ? $trust->logos : [],
//
//                /* =========================
//                   🔥 NEW: STATS
//                ========================== */
//                'stats' => $stats ? (
//                is_string($stats->items)
//                    ? json_decode($stats->items, true)
//                    : ($stats->items ?? [])
//                ) : [],
//
//                /* =========================
//                   🔥 NEW: TESTIMONIALS
//                ========================== */
//                'testimonials' => $testimonials ? (
//                is_string($testimonials->items)
//                    ? json_decode($testimonials->items, true)
//                    : ($testimonials->items ?? [])
//                ) : [],
//
//                /* =========================
//                   SERVICES SECTION
//                ========================== */
//                'servicesSection' => $serviceSection ? [
//                    'title' => $serviceSection->service_section_title,
//                    'slug' => $serviceSection->slug,
//                    'description' => $serviceSection->service_section_description,
//                    'image' => $this->getMediaUrl($serviceSection, 'services'),
//                ] : null,
//
//                /* =========================
//                   SERVICES LIST
//                ========================== */
//                'services' => $services->map(function ($service) {
//                    return [
//                        'title' => $service->title,
//                        'description' => $service->description,
//                        'slug' => $service->slug,
//                        'image' => $service->image_url,
//                    ];
//                }),
//            ];
//        });
//
//        return response()->json($data)
//            ->header('Cache-Control', 'public, max-age=300');
//    }
//
//    /**
//     * 🔥 REVALIDATE
//     */
//    public function revalidate()
//    {
//        Cache::forget('home_api');
//
//        Http::post('http://localhost:3000/api/revalidate', [
//            'tag' => 'home'
//        ]);
//
//        return response()->json([
//            'success' => true,
//            'message' => 'Home cache cleared & revalidated'
//        ]);
//    }
//
//    /**
//     * 🔒 Safe media getter
//     */
//    private function getMediaUrl($model, $collection)
//    {
//        if (!$model || !method_exists($model, 'getFirstMediaUrl')) {
//            return null;
//        }
//
//        return $model->getFirstMediaUrl($collection, 'webp') ?: null;
//    }
//}


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeStat;
use App\Models\HomeTestimonial;
use App\Models\HomeTrust;
use App\Models\ServiceSection;
use App\Models\HomeHeroSection;
use App\Models\HomeWhyUs;
use App\Models\HomeHowWork;
use App\Models\HomeCtaSection;
use App\Models\FaqSection;
use App\Models\Service;
use App\Models\SeoPage;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * GET HOME DATA + SEO
     */
    public function index()
    {
        /*
        |------------------------------------------------------------------
        | 1. CACHE ONLY CONTENT (NOT SEO)
        |------------------------------------------------------------------
        */
        $data = Cache::remember('home_api', 300, function () {

            $hero = HomeHeroSection::first();
            $why = HomeWhyUs::first();
            $how = HomeHowWork::first();
            $cta = HomeCtaSection::first();
            $faq = FaqSection::first();
            $services = Service::latest()->take(6)->get();
            $serviceSection = ServiceSection::first();

            $trust = HomeTrust::where('is_active', true)->latest()->first();
            $stats = HomeStat::where('is_active', true)->latest()->first();
            $testimonials = HomeTestimonial::where('is_active', true)->latest()->first();

            return [

                /* ========================= HERO ========================= */
                'homeHero' => $hero ? [
                    'title' => $hero->home_hero_title,
                    'description' => $hero->home_hero_description,
                    'list' => $hero->home_hero_list,
                    'call_button' => $hero->home_hero_call_button_text,
                    'call_number' => $hero->home_hero_call_button_number,
                    'service_button' => $hero->home_hero_service_button_text,
                    'image' => $hero->image_url,
                ] : null,

                /* ========================= WHY US ========================= */
                'whyUs' => $why ? [
                    'title' => $why->why_us_title,
                    'description' => $why->why_us_description,
                    'items' => $why->why_us_items ?? [],
                ] : null,

                /* ========================= HOW IT WORKS ========================= */
                'howWork' => $how ? [
                    'title' => $how->title,
                    'description' => $how->description,
                    'cards' => is_string($how->how_cards)
                        ? json_decode($how->how_cards, true)
                        : ($how->how_cards ?? []),
                ] : null,

                /* ========================= CTA ========================= */
                'Cta' => $cta ? [
                    'cta_title' => $cta->cta_title,
                    'cta_title_hilight' => $cta->cta_title_hilight,
                    'cta_description' => $cta->cta_description,
                    'cta_phone_btn_number' => $cta->cta_phone_btn_number,
                    'cta_phone_btn_text' => $cta->cta_phone_btn_text,
                    'cta_message_button_text' => $cta->cta_message_button_text,
                ] : null,

                /* ========================= FAQ ========================= */
                'Faq' => $faq ? [
                    'title' => $faq->title,
                    'description' => $faq->description,
                    'faq' => $faq->faq ?? [],
                ] : null,

                /* ========================= TRUST ========================= */
                'trust' => $trust ? $trust->logos : [],

                /* ========================= STATS ========================= */
                'stats' => $stats ? (
                is_string($stats->items)
                    ? json_decode($stats->items, true)
                    : ($stats->items ?? [])
                ) : [],

                /* ========================= TESTIMONIALS ========================= */
                'testimonials' => $testimonials ? (
                is_string($testimonials->items)
                    ? json_decode($testimonials->items, true)
                    : ($testimonials->items ?? [])
                ) : [],

                /* ========================= SERVICES SECTION ========================= */
                'servicesSection' => $serviceSection ? [
                    'title' => $serviceSection->service_section_title,
                    'slug' => $serviceSection->slug,
                    'description' => $serviceSection->service_section_description,
                    'image' => $serviceSection->image,
                ] : null,

                /* ========================= SERVICES ========================= */
                'services' => $services->map(function ($service) {
                    return [
                        'title' => $service->title,
                        'description' => $service->description,
                        'slug' => $service->slug,
                        'image' => $service->image,
                    ];
                }),
            ];
        });

        /*
        |------------------------------------------------------------------
        | 2. GET SEO (OUTSIDE CACHE)
        |------------------------------------------------------------------
        */
        $seo = SeoPage::getByKey('home');

        /*
        |------------------------------------------------------------------
        | 3. RESPONSE
        |------------------------------------------------------------------
        */
        return response()->json([
            'data' => $data,
            'seo' => [
                'meta' => $seo?->meta ?? [],
                'schema' => $seo?->schema_data ?? [],
            ]
        ])->header('Cache-Control', 'public, max-age=300');
    }

    /**
     * 🔥 REVALIDATE
     */
    public function revalidate()
    {
        Cache::forget('home_api');

        Http::post('http://localhost:3000/api/revalidate', [
            'tag' => 'home'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Home cache cleared & revalidated'
        ]);
    }

    /**
     * 🔒 Safe media getter
     */
    private function getMediaUrl($model, $collection)
    {
        if (!$model || !method_exists($model, 'getFirstMediaUrl')) {
            return null;
        }

        return $model->getFirstMediaUrl($collection, 'webp') ?: null;
    }
}
