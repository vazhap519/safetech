<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{




    public function index()
    {
        $settings = Settings::first();
        return response()->json([

            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */
            'socials' => $settings?->footer_brand_soc ?? [],
            'share' => $settings?->share_buttons ?? [],
            'headers' => $settings?->footer_headers ?? [],
            'contact' => $settings?->footer_contact_area ?? [],

            'brand_description' => $settings?->footer_brand_text ?? null,
            'share_title' => $settings?->share_title ?? null,
            'copy' => $settings?->footer_copyright_text ?? null,

            /*
            |--------------------------------------------------------------------------
            | CONTACT PAGE 🔥 (ეს გაკლდა)
            |--------------------------------------------------------------------------
            */
            'contact_page' => [
                'phone' => $settings?->contact_page_number,
                'whatsapp' => $settings?->contact_page_whatsapp,
                'viber' => $settings?->contact_page_viber,
                'email' => $settings?->contact_page_email,
                'address' => $settings?->contact_page_address,

                'hero_title' => $settings?->contact_page_hero_title,
                'hero_text' => $settings?->contact_page_hero_text,

                'why_title' => $settings?->contact_page_why_title,
                'why_items' => $settings?->contact_page_why_text ?? [],

//                'cta_title' => $settings?->contact_page_cta_title,
                'contact_info_title' => $settings?->contact_page_info_title,
            ],
        ]);
    }

}
