<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactSection;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        ContactSection::updateOrCreate(
            ['id' => 1], // singleton
            [

                // 📞 PHONE
                'contact_page_number' => '+995599000000',

                // 🔵 HERO
                'contact_page_hero_title' => 'დაგვიკავშირდით',
                'contact_page_hero_text' => 'ჩვენ მზად ვართ დაგეხმაროთ ნებისმიერ დროს',

                // 🟡 WHY
                'contact_page_why_title' => 'რატომ ჩვენ',
                'contact_page_why_text' => [
                    ['text' => 'სწრაფი მომსახურება'],
                    ['text' => 'გამოცდილი გუნდი'],
                    ['text' => 'მაღალი ხარისხი'],
                ],

                // ℹ️ INFO
                'contact_page_info_title' => 'საკონტაქტო ინფორმაცია',

                'contact_page_whatsapp' => '995599000000',
                'contact_page_viber' => '995599000000',
                'contact_page_email' => 'info@safetech.ge',
                'contact_page_address' => 'თბილისი, საქართველო',
            ]
        );
    }
}
