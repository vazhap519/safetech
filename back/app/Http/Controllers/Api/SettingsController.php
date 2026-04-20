<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SocialLinks;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        $shareButtons = SocialLinks::shareButtons($settings?->share_buttons ?? []);

        return response()->json([
            'favicon' => $settings?->favicon_url,
            'favicons' => $settings?->favicon_urls ?? [],
            'favicon_version' => $settings?->favicon_version,

            'socials' => SocialLinks::socials($settings?->footer_brand_soc ?? [], $settings),
            'headers' => $settings?->footer_headers ?? [],
            'contact' => $settings?->footer_contact_area ?? [],
            'brand_description' => $settings?->footer_brand_text ?? null,
            'copy' => $settings?->footer_copyright_text ?? null,

            'share' => [
                'share_title' => $settings?->share_title ?? null,
                'title' => $settings?->share_title ?? null,
                'share_buttons' => $shareButtons,
                'buttons' => $shareButtons,
            ],

            'seo' => [
                'local_business' => [
                    'phone' => $settings?->phone,
                    'email' => $settings?->email,
                    'address' => $settings?->address,
                    'city' => $settings?->city,
                    'country' => $settings?->country,
                    'lat' => $settings?->lat,
                    'lng' => $settings?->lng,
                    'open_time' => $settings?->open_time,
                    'close_time' => $settings?->close_time,
                ],
                'same_as' => SocialLinks::sameAs($settings),
            ],

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
                'contact_info_title' => $settings?->contact_page_info_title,
            ],
        ]);
    }
}
