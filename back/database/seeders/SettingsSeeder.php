<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [

                /*
                |--------------------------------------------------------------------------
                | SHARE
                |--------------------------------------------------------------------------
                */
                'share_title' => 'გააზიარე ეს გვერდი',

                'share_buttons' => [
                    ['type' => 'facebook'],
                    ['type' => 'whatsapp'],
                    ['type' => 'telegram'],
                    ['type' => 'linkedin'],
                    ['type' => 'twitter'],
                    ['type' => 'link'],
                ],

                /*
                |--------------------------------------------------------------------------
                | FOOTER (test data)
                |--------------------------------------------------------------------------
                */
                'footer_brand_text' => 'Safetech - უსაფრთხოების სისტემები',
                'footer_copyright_text' => '© 2026 Safetech',

                'footer_brand_soc' => [
                    [
                        'icon' => 'FaFacebook',
                        'url' => 'https://facebook.com',
                        'text' => 'Facebook',
                        'bg_color' => '#1877F2',
                        'hover_color' => '#0d65d9',
                    ],
                    [
                        'icon' => 'FaInstagram',
                        'url' => 'https://instagram.com',
                        'text' => 'Instagram',
                        'bg_color' => '#E4405F',
                        'hover_color' => '#c72e4d',
                    ],
                ],

//                /*
//                |--------------------------------------------------------------------------
//                | CONTACT PAGE (optional test)
//                |--------------------------------------------------------------------------
//                */
//                'contact_page_number' => '+995599123456',
//                'contact_page_email' => 'info@safetech.ge',
//                'contact_page_address' => 'Tbilisi, Georgia',
            ]
        );
    }
}
