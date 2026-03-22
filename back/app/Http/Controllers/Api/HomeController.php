<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\HomeHeroSection;
use \App\Models\HomeWhyUs;
use \App\Models\HomeHowWork;
class HomeController extends Controller
{
    public function index()
{
    $hero = HomeHeroSection::first();
$why=HomeWhyUs::first();
$how=HomeHowWork::first();

    if (!$hero) {
        return response()->json([
            'message' => 'ინფორმაცია არ მოიძებნა'
        ], 404);
    }
    if(!$why){
        return response()->json([
           'message' => 'ინფორმაცია არ მოიძებნა'
        ],404);
    }
if(!$how){
        return response()->json([
           'message' => 'ინფორმაცია არ მოიძებნა'
        ],404);
    }
    return response()->json([
        'homeHero' => [
        'title' => $hero->home_hero_title,
        'description' => $hero->home_hero_description,
        'list' => $hero->home_hero_list,
        'call_button' => $hero->home_hero_call_button_text,
        'call__number'=>$hero->home_hero_call_button_number,
        'service_button' => $hero->home_hero_service_button_text,

        // 🔥 WebP image
        'image' => $hero->getFirstMediaUrl('hero', 'webp'),
        ],
    'HomeWhyUs' => [
        'why_us_title' => $why->why_us_title,
        'why_us_description' => $why->why_us_description,
        'why_us_items' => $why->why_us_items ?? [],
    ],
    'how'=>[
        'title'=>$how->ttitle,
    ]
    ]);
}
}
