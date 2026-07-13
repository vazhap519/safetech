<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'contact' => [
                'phone' => '',
                'email' => '',
                'address' => '',
                'whatsapp' => '',
                'whatsapp_message' => '',
                'hours' => '',
                'lead_email' => 'safetechgeorgia@gmail.com',
            ],
            'socials' => [
                'links' => [],
            ],
            'seo' => [
                'site_name' => 'SafeTech',
                'default_image' => null,
            ],
            'branding' => [
                'site_name' => 'SafeTech',
                'tagline' => '',
                'logo' => null,
                'footer_logo' => null,
                'favicon' => null,
                'default_image' => null,
            ],
            'translations' => [
                'entries' => [],
            ],
        ] as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => 'general',
                    'value' => $value,
                    'is_public' => true,
                ],
            );
        }

        $services = [
            [
                'slug' => 'cctv',
                'name' => 'CCTV',
                'eyebrow' => 'Security systems',
                'icon' => 'videocam',
                'title' => 'CCTV installation and monitoring',
                'description' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.',
                'short_description' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.',
                'long_description' => 'We design, install, and maintain CCTV systems for business-critical spaces.',
                'seo_description' => 'Professional CCTV installation, camera setup, recording, and monitoring services in Georgia.',
                'seo' => [
                    'title' => 'CCTV installation and monitoring',
                    'description' => 'Professional CCTV installation, camera setup, recording, and monitoring services in Georgia.',
                ],
                'keywords' => ['CCTV', 'security cameras', 'video monitoring'],
                'highlights' => ['Site audit', 'Camera placement plan', 'Recording setup'],
                'overview' => [
                    'title' => 'Reliable video security',
                    'paragraphs' => ['We design, install, and maintain CCTV systems for business-critical spaces.'],
                    'stats' => [],
                ],
                'benefits' => [],
                'solutions' => [],
                'industries' => ['Offices', 'Retail', 'Warehouses'],
                'process' => [],
                'brands' => [],
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'networking',
                'name' => 'Network Infrastructure',
                'eyebrow' => 'IT infrastructure',
                'icon' => 'lan',
                'title' => 'Network Infrastructure',
                'description' => 'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.',
                'short_description' => 'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.',
                'long_description' => 'We build reliable wired and wireless networks for growing teams.',
                'seo_description' => 'Business network infrastructure, structured cabling, Wi-Fi, router, and switch setup in Georgia.',
                'seo' => [
                    'title' => 'Network Infrastructure',
                    'description' => 'Business network infrastructure, structured cabling, Wi-Fi, router, and switch setup in Georgia.',
                ],
                'keywords' => ['networking', 'structured cabling', 'Wi-Fi'],
                'highlights' => ['Stable coverage', 'Managed switching', 'Secure routing'],
                'overview' => [
                    'title' => 'Stable networks for daily operations',
                    'paragraphs' => ['We build reliable wired and wireless networks for growing teams.'],
                    'stats' => [],
                ],
                'benefits' => [],
                'solutions' => [],
                'industries' => ['Offices', 'Hotels', 'Warehouses'],
                'process' => [],
                'brands' => [],
                'is_published' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($services as $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                $service,
            );
        }

        Project::query()->updateOrCreate(
            ['slug' => 'office-network-upgrade'],
            [
                'name' => 'Office Network Upgrade',
                'title' => 'Office Network Upgrade',
                'description' => 'A complete network refresh with structured cabling, managed switching, and Wi-Fi coverage.',
                'seo_description' => 'SafeTech office network upgrade case study with structured cabling and managed Wi-Fi.',
                'image_alt' => 'Office network upgrade project',
                'category' => 'offices',
                'technology' => 'Structured cabling, switching, Wi-Fi',
                'icon' => 'business',
                'accent' => 'primary',
                'meta' => [],
                'scope' => [],
                'specs' => [],
                'challenges' => [],
                'solutions' => [],
                'process' => [],
                'gallery' => [],
                'results' => [],
                'testimonial' => [],
                'related' => [],
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ],
        );
    }
}
