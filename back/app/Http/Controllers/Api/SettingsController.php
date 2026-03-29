<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * ✅ Single Source of Truth
     */
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

    public function index()
    {
        $settings = Setting::first();

        /**
         * ✅ Transform share buttons (CRITICAL FIX)
         */
        $shareButtons = collect($settings?->share_buttons ?? [])
            ->map(function ($btn) {
                $map = $this->shareMap();

                return $map[$btn['type']] ?? null;
            })
            ->filter()
            ->values();

        return response()->json([

            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */
            'socials' => $settings?->footer_brand_soc ?? [],
            'headers' => $settings?->footer_headers ?? [],
            'contact' => $settings?->footer_contact_area ?? [],

            'brand_description' => $settings?->footer_brand_text ?? null,
            'copy' => $settings?->footer_copyright_text ?? null,

            /*
            |--------------------------------------------------------------------------
            | ✅ SHARE (FIXED)
            |--------------------------------------------------------------------------
            */
            'share' => [
                'share_title' => $settings?->share_title ?? null,
                'share_buttons' => $shareButtons,
            ],

            /*
            |--------------------------------------------------------------------------
            | CONTACT PAGE
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

                'contact_info_title' => $settings?->contact_page_info_title,
            ],
        ]);
    }
}
