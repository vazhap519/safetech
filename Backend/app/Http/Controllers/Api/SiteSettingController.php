<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first();

        return response()->json([
            'SiteName'=>[
                'SiteName'=>$settings?->site_name,
            ],
            // Titles
            'titles' => [
                'services' => $settings?->our_services,
                'projects' => $settings?->recent_projets,
                'reviews' => $settings?->client_reviews,
                'blog' => $settings?->latest_articles,
                'contact' => $settings?->get_consultation,
            ],

            // Topbar
            'topbar' => [
                'text' => $settings?->top_bar_consultation_text,
                'phone' => $settings?->top_bar_number,
            ],

            // Navigation
            'navigation' => [
                'services' => $settings?->navigation_services,
                'projects' => $settings?->navigation_projects,
                'about' => $settings?->navigation_about,
                'blog' => $settings?->navigation_blog,
                'contact' => $settings?->navigation_contact,
            ],

            // Footer
            'footer' => [
                'services' => $settings?->footer_services,
                'contact' => $settings?->footer_contact,
                'company' => $settings?->footer_company,
                'description' => $settings?->footer_description,
            ],

            // Styles
            'styles' => $settings?->styles,

            // 🔥 Spatie Media
//             'logo' => url($settings?->getFirstMediaUrl('logo', 'logo_webp')),
// 'favicon' => url($settings?->getFirstMediaUrl('favicon', 'favicon_webp')),
'logo' => str_replace('127.0.0.1', 'localhost', url($settings?->getFirstMediaUrl('logo', 'logo_webp'))),
'favicon' => str_replace('127.0.0.1', 'localhost', url($settings?->getFirstMediaUrl('favicon', 'favicon_webp'))),
        ]);
    }
}