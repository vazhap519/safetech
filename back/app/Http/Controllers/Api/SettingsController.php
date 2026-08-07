<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\SiteSettings;
use App\Support\SiteSettingValueNormalizer;
use App\Support\SocialLinks;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = SiteSettings::businessProfile();
        $branding = SiteSettings::value('branding');
        $contact = SiteSettingValueNormalizer::normalize(
            'contact',
            SiteSettings::value('contact'),
        );
        unset($contact['lead_email']);
        $contact['phone'] = $settings->phone;
        $contact['phones'] = is_array($settings->phones ?? null) ? $settings->phones : [];
        $socials = SiteSettingValueNormalizer::normalize(
            'socials',
            SiteSettings::value('socials'),
        );
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
                'enabled' => $settings->share_enabled ?? true,
                'show_on_services' => $settings->share_on_services ?? true,
                'show_on_projects' => $settings->share_on_projects ?? true,
                'share_title' => $socials['share_title'] ?? null,
                'title' => $socials['share_title'] ?? null,
                'titles' => [
                    'ka' => $settings->share_title_ka ?? null,
                    'en' => $settings->share_title_en ?? null,
                    'ru' => $settings->share_title_ru ?? null,
                ],
                'share_buttons' => $shareButtons,
                'buttons' => $shareButtons,
            ],

            'seo' => [
                'local_business' => [
                    'phone' => $settings->phone,
                    'phones' => $settings->phones ?? [],
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
                'phones' => $contact['phones'] ?? [],
                'whatsapp' => $contact['whatsapp'] ?? null,
                'whatsapp_enabled' => $contact['whatsapp_enabled'] ?? false,
                'whatsapp_message' => $contact['whatsapp_message'] ?? null,
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
