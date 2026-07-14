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
        foreach ($this->defaultSiteSettings() as $key => $value) {
            $setting = SiteSetting::query()->firstOrCreate(
                ['key' => $key],
                [
                    'group' => 'general',
                    'value' => $value,
                    'is_public' => true,
                ],
            );

            if ($key === 'translations') {
                $value = $this->mergeMissingTranslationEntries(
                    $setting->value,
                    $value,
                );

                $setting->forceFill([
                    'group' => 'general',
                    'value' => $value,
                    'is_public' => true,
                ])->save();
            }
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
            Service::query()->firstOrCreate(
                ['slug' => $service['slug']],
                $service,
            );
        }

        Project::query()->firstOrCreate(
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

    private function defaultSiteSettings(): array
    {
        return [
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
                'entries' => $this->defaultTranslationEntries(),
            ],
        ];
    }

    private function defaultTranslationEntries(): array
    {
        return [
            ['key' => 'nav.home', 'ka' => 'მთავარი', 'en' => 'Home', 'ru' => 'Главная'],
            ['key' => 'nav.services', 'ka' => 'სერვისები', 'en' => 'Services', 'ru' => 'Услуги'],
            ['key' => 'nav.projects', 'ka' => 'პროექტები', 'en' => 'Projects', 'ru' => 'Проекты'],
            ['key' => 'nav.about', 'ka' => 'ჩვენ შესახებ', 'en' => 'About', 'ru' => 'О нас'],
            ['key' => 'nav.contact', 'ka' => 'კონტაქტი', 'en' => 'Contact', 'ru' => 'Контакты'],
            ['key' => 'nav.consultation', 'ka' => 'კონსულტაცია', 'en' => 'Consultation', 'ru' => 'Консультация'],

            ['key' => 'home.hero.eyebrow', 'ka' => 'ბიზნეს IT გადაწყვეტილებები', 'en' => 'Enterprise IT Solutions', 'ru' => 'Корпоративные IT-решения'],
            ['key' => 'home.hero.titlePrefix', 'ka' => 'თანამედროვე ბიზნესის უსაფრთხო', 'en' => 'Secure', 'ru' => 'Надежная'],
            ['key' => 'home.hero.titleAccent', 'ka' => 'IT ინფრასტრუქტურა', 'en' => 'IT infrastructure for modern business', 'ru' => 'IT-инфраструктура для современного бизнеса'],
            ['key' => 'home.hero.description', 'ka' => 'ვიდეოსამეთვალყურეო, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალური გადაწყვეტები თანამედროვე ბიზნესისთვის.', 'en' => 'Professional CCTV, access control, networking, and server infrastructure solutions for modern businesses.', 'ru' => 'Профессиональные решения для видеонаблюдения, контроля доступа, сетевой и серверной инфраструктуры для современного бизнеса.'],
            ['key' => 'home.hero.primaryCta', 'ka' => 'კონსულტაციის მოთხოვნა', 'en' => 'Request Consultation', 'ru' => 'Запросить консультацию'],
            ['key' => 'home.hero.secondaryCta', 'ka' => 'სერვისების ნახვა', 'en' => 'View Services', 'ru' => 'Смотреть услуги'],
            ['key' => 'home.hero.imageAlt', 'ka' => 'SafeTech-ის უსაფრთხოებისა და IT ინფრასტრუქტურის გადაწყვეტა', 'en' => 'SafeTech security and IT infrastructure solution', 'ru' => 'Решение SafeTech для безопасности и IT-инфраструктуры'],

            ['key' => 'home.services.eyebrow', 'ka' => 'ძირითადი შესაძლებლობები', 'en' => 'Our Capabilities', 'ru' => 'Наши возможности'],
            ['key' => 'home.services.title', 'ka' => 'ძირითადი სერვისები', 'en' => 'Core Services', 'ru' => 'Основные услуги'],
            ['key' => 'home.services.description', 'ka' => 'თანამედროვე უსაფრთხოების, ქსელური და IT ინფრასტრუქტურის პროფესიონალური გადაწყვეტილებები ბიზნესისთვის.', 'en' => 'Professional security, networking, and IT infrastructure solutions for business.', 'ru' => 'Профессиональные решения для безопасности, сетевой и IT-инфраструктуры бизнеса.'],
            ['key' => 'home.projects.eyebrow', 'ka' => 'გამორჩეული ნამუშევრები', 'en' => 'Featured Work', 'ru' => 'Избранные проекты'],
            ['key' => 'home.projects.title', 'ka' => 'განხორციელებული პროექტები', 'en' => 'Completed Projects', 'ru' => 'Реализованные проекты'],
            ['key' => 'home.projects.description', 'ka' => 'თანამედროვე უსაფრთხოების, ქსელური და IT ინფრასტრუქტურის წარმატებით განხორციელებული პროექტები.', 'en' => 'Successfully delivered security, networking, and IT infrastructure projects.', 'ru' => 'Успешно реализованные проекты по безопасности, сетевой и IT-инфраструктуре.'],

            ['key' => 'meta.home.title', 'ka' => 'IT ინფრასტრუქტურა და უსაფრთხოების სისტემები ბიზნესისთვის', 'en' => 'IT Infrastructure and Security Systems for Business', 'ru' => 'IT-инфраструктура и системы безопасности для бизнеса'],
            ['key' => 'meta.home.description', 'ka' => 'SafeTech უზრუნველყოფს ვიდეომეთვალყურეობას, დაშვების კონტროლს, ქსელურ და სერვერულ ინფრასტრუქტურას საქართველოში.', 'en' => 'SafeTech delivers CCTV, access control, networking, and server infrastructure solutions for businesses in Georgia.', 'ru' => 'SafeTech внедряет видеонаблюдение, контроль доступа, сетевую и серверную инфраструктуру для бизнеса в Грузии.'],
            ['key' => 'meta.services.title', 'ka' => 'CCTV, ქსელები და IT სერვისები | SafeTech', 'en' => 'CCTV, Networking, and IT Services | SafeTech', 'ru' => 'CCTV, сети и IT-услуги | SafeTech'],
            ['key' => 'meta.projects.title', 'ka' => 'განხორციელებული IT და უსაფრთხოების პროექტები | SafeTech', 'en' => 'Completed IT and Security Projects | SafeTech', 'ru' => 'Реализованные IT- и охранные проекты | SafeTech'],
            ['key' => 'meta.about.title', 'ka' => 'ჩვენ შესახებ | SafeTech გუნდი და გამოცდილება', 'en' => 'About SafeTech | Team and Experience', 'ru' => 'О SafeTech | Команда и опыт'],
            ['key' => 'meta.contact.title', 'ka' => 'კონტაქტი და კონსულტაცია | SafeTech', 'en' => 'Contact and Consultation | SafeTech', 'ru' => 'Контакты и консультация | SafeTech'],

            ['key' => 'service.cctv.card.title', 'ka' => 'ვიდეოსამეთვალყურეო სისტემები', 'en' => 'CCTV Systems', 'ru' => 'Системы видеонаблюдения'],
            ['key' => 'service.cctv.card.description', 'ka' => 'პროფესიონალური კამერები ოფისებისთვის, რიტეილისთვის, საწყობებისთვის და საცხოვრებელი სივრცეებისთვის.', 'en' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.', 'ru' => 'Профессиональные камеры для офисов, ритейла, складов и жилых объектов.'],
            ['key' => 'service.networking.card.title', 'ka' => 'ქსელური ინფრასტრუქტურა', 'en' => 'Network Infrastructure', 'ru' => 'Сетевая инфраструктура'],
            ['key' => 'service.networking.card.description', 'ka' => 'სტრუქტურული კაბელირება, როუტერები, სვიჩები, Wi-Fi დაფარვა და დაცული ბიზნეს ქსელები.', 'en' => 'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.', 'ru' => 'Структурированная кабельная система, роутеры, свичи, Wi-Fi покрытие и защищенные бизнес-сети.'],
        ];
    }

    private function mergeMissingTranslationEntries(mixed $currentValue, array $defaults): array
    {
        if (! is_array($currentValue)) {
            $currentValue = [];
        }

        $currentEntries = $currentValue['entries'] ?? [];
        $defaultEntries = $defaults['entries'] ?? [];

        if (! is_array($currentEntries)) {
            $currentEntries = [];
        }

        $existingKeys = array_filter(array_column($currentEntries, 'key'));

        foreach ($defaultEntries as $entry) {
            if (! is_array($entry) || empty($entry['key'])) {
                continue;
            }

            if (! in_array($entry['key'], $existingKeys, true)) {
                $currentEntries[] = $entry;
                $existingKeys[] = $entry['key'];
            }
        }

        return array_merge($currentValue, ['entries' => $currentEntries]);
    }
}
