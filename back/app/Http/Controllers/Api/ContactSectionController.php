<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSection; // ან შენი model (ContactSection თუ გაქვს)

class ContactSectionController extends Controller
{
    public function index()
    {
        $data = ContactSection::first(); // ან ContactSection::first()

        return response()->json([
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
        ]);
    }
}
