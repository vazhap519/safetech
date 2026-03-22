<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\HomeHeroSection;
use \App\Models\HomeWhyUs;
use \App\Models\HomeHowWork;
use \App\Models\HomeCtaSection;
use \App\Models\FaqSection;
use App\Models\Service;
class HomeController extends Controller
{
    public function index()
{
    $hero = HomeHeroSection::first();
$why=HomeWhyUs::first();
$how=HomeHowWork::first();
$cta=HomeCtaSection::first();
$faq=FaqSection::first();
$services = Service::all();
$serviceSection=ServiceSection::first();
   return response()->json([
    'homeHero' => $hero ? [
        'title' => $hero->home_hero_title,
        'description' => $hero->home_hero_description,
        'list' => $hero->home_hero_list,
        'call_button' => $hero->home_hero_call_button_text,
        'call_number' => $hero->home_hero_call_button_number,
        'service_button' => $hero->home_hero_service_button_text,
        'image' => $hero->getFirstMediaUrl('hero', 'webp'),
    ] : null,

    'whyUs' => $why ? [
        'title' => $why->why_us_title,
        'description' => $why->why_us_description,
        'items' => $why->why_us_items ?? [],
    ] : null,

    'howWork' => $how ? [
        'title' => $how->title,
        'description' => $how->description,
        'cards' => is_string($how->how_cards)
            ? json_decode($how->how_cards, true)
            : $how->how_cards ?? [],
    ] : null,
    'Cta'=>$cta?[
        'cta_title'=>$cta->cta_title,
        'cta_title_hilight'=>$cta->cta_title_hilight,
        'cta_description'=>$cta->cta_description,
        'cta_phone_btn_number'=>$cta->cta_phone_btn_number,
        'cta_phone_btn_text'=>$cta->cta_phone_btn_text,
        'cta_message_button_text'=>$cta->cta_message_button_text,
    ]:null,

    'Faq'=>$faq?[
        'title'=>$faq->title,
'description'=>$faq->description,
'faq'=>$faq->faq?? [],
    ]:null,
'servicesSection' => $serviceSection ? [
    'title' => $serviceSection->service_section_title,
    'description' => $serviceSection->service_section_description,
] : null,

    'services' => $services->map(function ($service) {
    return [
        'title' => $service->title,
        'description' => $service->description,
        'slug' => $service->slug,
        'image' => $service->getFirstMediaUrl('services', 'webp'),
    ];
}),
]);
}
}
