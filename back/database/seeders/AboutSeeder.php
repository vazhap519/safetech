<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\About;

class AboutSeeder extends Seeder
{
    public function run(): void
    {
        About::create([
            // Hero
            'hero_title' => 'ჩვენს შესახებ',
            'hero_description' => '<p>ჩვენ ვართ პროფესიონალური IT კომპანია...</p>',
            'hero_badge' => 'სანდო პარტნიორი',
            'hero_trust_list' => [
                ['hero_trust_list_title' => '10+ წელი გამოცდილება'],
                ['hero_trust_list_title' => '500+ კმაყოფილი მომხმარებელი'],
            ],

            // Story
            'story_title' => 'ჩვენი ისტორია',
            'story_title_description' => 'როგორ დავიწყეთ',
            'story_content' => '<p>ჩვენი კომპანია დაარსდა...</p>',
            'story_stats' => [
                [
                    'story_stats_label' => 'პროექტები',
                    'story_stats_numbers' => '120+',
                ],
                [
                    'story_stats_label' => 'კლიენტები',
                    'story_stats_numbers' => '300+',
                ],
            ],

            // Why Us
            'why_us_title' => 'რატომ ჩვენ',
            'why_us_title_description' => 'ჩვენი უპირატესობები',
            'why_us_content' => [
                [
                    'title' => 'სწრაფი სერვისი',
                    'desc' => 'მოკლე დროში შესრულება',
                    'why_us_icons' => 'FaBolt',
                ],
                [
                    'title' => 'პროფესიონალიზმი',
                    'desc' => 'გამოცდილი გუნდი',
                    'why_us_icons' => 'FaUsers',
                ],
            ],

            // CTA
            'cta_title' => 'დაგვიკავშირდით',
            'cta_title_description' => 'ჩვენ მზად ვართ დაგეხმაროთ',
            'cta_trust' => [
                ['cta_trust_title' => 'უფასო კონსულტაცია'],
                ['cta_trust_title' => '24/7 მხარდაჭერა'],
            ],
            'cta_phone' => '+995599000000',
        ]);
    }
}
