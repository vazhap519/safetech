<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// ✅ სწორად აქ
use App\Models\Header;
use App\Models\Hero;
use App\Models\QuickCta;
class HomeController extends Controller
{
    public function index()
    {
        // $header = Header::first();
        $hero = Hero::first();
$cta = QuickCta::first();
        if ( !$hero) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'data' => [
              

                'hero' => $hero ? [
                    'badge' => $hero->badge,
                    'title' => $hero->title,
                    'subtitle' => $hero->subtitle,

                    'primary_text' => $hero->primary_text,
                    'primary_link' => $hero->primary_link,

                    'secondary_text' => $hero->secondary_text,
                    'secondary_link' => $hero->secondary_link,

                    'image' => $hero->getFirstMediaUrl('hero', 'webp')
                ] : null,
                'cta' => $cta
            ]
        ]);
    }
}