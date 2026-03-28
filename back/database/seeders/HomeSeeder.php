<?php

namespace Database\Seeders;

use App\Models\FaqSection;
use Illuminate\Database\Seeder;
use App\Models\HomeHeroSection;
use App\Models\HomeWhyUs;
use App\Models\HomeHowWork;
use App\Models\HomeCtaSection;
use App\Models\HomeStat;
use App\Models\HomeTrust;
use App\Models\HomeTestimonial;

class HomeSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * 🟢 HERO
         */
        HomeHeroSection::updateOrCreate(
            ['id' => 1],
            [
                'home_hero_title' => 'პროფესიონალური IT სერვისები',
                'home_hero_description' => 'ჩვენ გთავაზობთ სწრაფ და სანდო IT მომსახურებას',
                'home_hero_list' => [
                    ['text' => 'ქსელების გამართვა'],
                    ['text' => 'ვიდეო კამერები'],
                    ['text' => 'სერვერების კონფიგურაცია'],
                ],
                'home_hero_call_button_text' => 'დაგვიკავშირდით',
                'home_hero_call_button_number' => '+995599000000',
                'home_hero_service_button_text' => 'სერვისები',
            ]
        );

        /**
         * 🔵 WHY US
         */
        HomeWhyUs::updateOrCreate(
            ['id' => 1],
            [
                'why_us_title' => 'რატომ ჩვენ',
                'why_us_description' => 'ჩვენი უპირატესობები',
                'why_us_items' => [
                    [
                        'title' => 'სწრაფი რეაგირება',
                        'desc' => 'მოკლე დროში მომსახურება',
                        'icon' => 'FaBolt',
                    ],
                    [
                        'title' => 'გამოცდილი გუნდი',
                        'desc' => 'პროფესიონალები თქვენს სამსახურში',
                        'icon' => 'FaUsers',
                    ],
                ],
            ]
        );

        /**
         * 🟡 HOW WORK
         */
        HomeHowWork::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'როგორ ვმუშაობთ',
                'description' => 'მარტივი 3 ნაბიჯი',
                'how_cards' => [
                    [
                        'title' => 'დაგვიკავშირდით',
                        'desc' => 'დარეკეთ ან მოგვწერეთ',
                    ],
                    [
                        'title' => 'ვაფასებთ პრობლემას',
                        'desc' => 'სწრაფი დიაგნოსტიკა',
                    ],
                    [
                        'title' => 'ვაგვარებთ',
                        'desc' => 'სრულყოფილი გადაწყვეტა',
                    ],
                ],
            ]
        );

        /**
         * 🔴 CTA
         */
        HomeCtaSection::updateOrCreate(
            ['id' => 1],
            [
                'cta_title' => 'დაგვიკავშირდით ახლავე',
                'cta_title_hilight' => 'დღესვე',
                'cta_description' => 'ჩვენ მზად ვართ დაგეხმაროთ',
                'cta_phone_btn_number' => '+995599000000',
                'cta_phone_btn_text' => 'დარეკვა',
                'cta_message_button_text' => 'მოგვწერეთ',
            ]
        );

        /**
         * 🟢 STATS
         */
        HomeStat::updateOrCreate(
            ['id' => 1],
            [
                'is_active' => true,
                'items' => [
                    [
                        'value' => '120+',
                        'label' => 'პროექტი',
                    ],
                    [
                        'value' => '300+',
                        'label' => 'კლიენტი',
                    ],
                ],
            ]
        );

        /**
         * 🔵 TRUST (ლოგოები)
         */
        HomeTrust::updateOrCreate(
            ['id' => 1],
            [
                'is_active' => true,
            ]
        );

        /**
         * 🟡 TESTIMONIALS
         */
        HomeTestimonial::updateOrCreate(
            ['id' => 1],
            [
                'is_active' => true,
                'items' => [
                    [
                        'name' => 'გიორგი',
                        'text' => 'ძალიან სწრაფი და ხარისხიანი მომსახურება',
                    ],
                    [
                        'name' => 'ანა',
                        'text' => 'პროფესიონალური გუნდი, რეკომენდაციას ვუწევ',
                    ],
                ],
            ]
        );
        FaqSection::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'ხშირად დასმული კითხვები',
                'description' => 'პასუხები თქვენს კითხვებზე',
                'faq' => [
                    [
                        'q' => 'რა დრო სჭირდება მომსახურებას?',
                        'a' => 'უმეტეს შემთხვევაში იმავე დღეს.',
                    ],
                    [
                        'q' => 'მუშაობთ 24/7?',
                        'a' => 'დიახ, გადაუდებელ შემთხვევებში.',
                    ],
                ],
            ]
        );
    }
}
