<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([

            // 🔗 Share
            'share_title' => 'გააზიარე',

            'share_buttons' => [
                [
                    'share_button_type' => 'facebook',
                    'name' => 'Facebook',
                    'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                    'color' => 'bg-blue-600',
                    'icon' => 'FaFacebook',
                ],
                [
                    'share_button_type' => 'whatsapp',
                    'name' => 'WhatsApp',
                    'url' => 'https://wa.me/?text={url}',
                    'color' => 'bg-green-500',
                    'icon' => 'FaWhatsapp',
                ],
                [
                    'share_button_type' => 'telegram',
                    'name' => 'Telegram',
                    'url' => 'https://t.me/share/url?url={url}',
                    'color' => 'bg-sky-500',
                    'icon' => 'FaTelegram',
                ],
                [
                    'share_button_type' => 'linkedin',
                    'name' => 'LinkedIn',
                    'url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                    'color' => 'bg-blue-700',
                    'icon' => 'FaLinkedin',
                ],
            ],

            // 🦶 Footer
            'footer_copyright_text' => '© 2026 ყველა უფლება დაცულია',
            'footer_brand_text' => 'My Company',

            'footer_brand_soc' => [
                [
                    'icon' => 'FaFacebook',
                    'url' => 'https://facebook.com',
                    'text' => 'Facebook',
                    'bg_color' => '#1877F2',
                    'hover_color' => '#0e5ec9',
                ],
                [
                    'icon' => 'FaInstagram',
                    'url' => 'https://instagram.com',
                    'text' => 'Instagram',
                    'bg_color' => '#E4405F',
                    'hover_color' => '#c13584',
                ],
                [
                    'icon' => 'FaLinkedin',
                    'url' => 'https://linkedin.com',
                    'text' => 'LinkedIn',
                    'bg_color' => '#0077B5',
                    'hover_color' => '#005582',
                ],
            ],


        ]);
    }
}
