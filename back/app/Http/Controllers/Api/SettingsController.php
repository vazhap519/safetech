<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(){
        $settings = Settings::first();

        return response()->json(
            [
                'socials' => $settings?->footer_brand_soc ?? [],
                'share'=>$settings->share_buttons ?? [],
'headers'=>$settings->footer_headers ?? [],
'contact'=>$settings->footer_contact_area ?? [],

                'brand_description' => $settings->footer_brand_text ?? null,
                'share_title' => $settings->share_title ?? null,
                'copy' => $settings->footer_copyright_text ?? null,
            ]
        );
    }
}
