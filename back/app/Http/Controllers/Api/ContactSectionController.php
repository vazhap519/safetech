<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSection;
use App\Models\SeoPage;
use Illuminate\Support\Facades\Cache;

class ContactSectionController extends Controller
{
    public function index()
    {
        $data = Cache::remember('contact_page', 300, function () {
            return ContactSection::first();
        });

        // ✅ SEO წამოღება
        $seo = SeoPage::getByKey('contact');

        if (!$data) {
            return response()->json([
                'data' => [
                    'hero' => null,
                    'phone' => null,
                    'why' => null,
                    'info' => null,
                ],
                'seo' => [
                    'meta' => $seo?->meta ?? [],
                    'schema' => $seo?->schema_data ?? [],
                ],
            ]);
        }

        return response()->json([

            // 🔥 მთავარი სტრუქტურა
            'data' => [
                'hero' => [
                    'title' => $data->contact_page_hero_title,
                    'text' => $data->contact_page_hero_text,
                ],

                'phone' => $data->contact_page_number,

                'why' => [
                    'title' => $data->contact_page_why_title,
                    'items' => $data->contact_page_why_text ?? [],
                ],

                'info' => [
                    'title' => $data->contact_page_info_title,
                    'whatsapp' => $data->contact_page_whatsapp,
                    'viber' => $data->contact_page_viber,
                    'email' => $data->contact_page_email,
                    'address' => $data->contact_page_address,
                ],
            ],

            // 🔥 SEO BLOCK
            'seo' => [
                'meta' => $seo?->meta ?? [],
                'schema' => $seo?->schema_data ?? [],
            ],
        ]);
    }
}
