<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\SocialLinks;
use App\Support\SiteSettings;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSettings::businessProfile();
        $branding = SiteSettings::value('branding');
        $contact = SiteSettings::value('contact');
        $socials = SiteSettings::value('socials');
        $shareButtons = SocialLinks::shareButtons($settings->share_buttons ?? []);

        return response()->json([
            'favicon' => $branding['favicon'] ?? null,
            'favicons' => [],
            'favicon_version' => 1,

            'socials' => SocialLinks::socials([], $settings),
            'headers' => [],
            'contact' => $contact,
            'brand_description' => $branding['tagline'] ?? null,
            'copy' => null,

            'share' => [
                'share_title' => $socials['share_title'] ?? null,
                'title' => $socials['share_title'] ?? null,
                'share_buttons' => $shareButtons,
                'buttons' => $shareButtons,
            ],

            'seo' => [
                'local_business' => [
                    'phone' => $settings->phone,
                    'email' => $settings->email,
                    'address' => $settings->address,
                    'city' => $settings->city,
                    'country' => $settings->country,
                    'lat' => $settings->lat,
                    'lng' => $settings->lng,
                    'open_time' => $settings->open_time,
                    'close_time' => $settings->close_time,
                ],
                'same_as' => SocialLinks::sameAs($settings),
            ],

            'contact_page' => [
                'phone' => $contact['phone'] ?? null,
                'whatsapp' => $contact['whatsapp'] ?? null,
                'viber' => null,
                'email' => $contact['email'] ?? null,
                'address' => $contact['address'] ?? null,
                'hero_title' => null,
                'hero_text' => null,
                'why_title' => null,
                'why_items' => [],
                'contact_info_title' => null,
            ],
        ]);
    }
}
