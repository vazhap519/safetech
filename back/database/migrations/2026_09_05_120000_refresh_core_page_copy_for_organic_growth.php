<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->refreshCoreSeoPages();
        $this->refreshCorePageCopy();
    }

    private function refreshCoreSeoPages(): void
    {
        if (! Schema::hasTable('seo_pages')) {
            return;
        }

        $pages = [
            'home' => [
                'slug' => '/',
                'title' => 'კამერების მონტაჟი, ქსელები და IT მომსახურება საქართველოში | SafeTech',
                'description' => 'SafeTech — უსაფრთხოების კამერების მონტაჟი, ქსელის გაყვანა, Wi‑Fi, დაშვების კონტროლი, შლაგბაუმები, POS და IT მომსახურება თბილისში, ხაშურში და საქართველოს რეგიონებში.',
                'keywords' => ['კამერების მონტაჟი', 'ვიდეოკამერების მონტაჟი', 'უსაფრთხოების კამერები თბილისი', 'ქსელის გაყვანა', 'WiFi გამართვა', 'IT მომსახურება', 'შლაგბაუმის მონტაჟი'],
                'schema_type' => 'WebSite',
            ],
            'about' => [
                'slug' => '/about',
                'title' => 'SafeTech Georgia — უსაფრთხოების სისტემებისა და IT მომსახურების გუნდი',
                'description' => 'გაიცანით SafeTech-ის გამოცდილება უსაფრთხოების კამერების, ქსელების, დაშვების კონტროლის, ავტომატიზაციისა და IT ინფრასტრუქტურის დაგეგმვა-მონტაჟში საქართველოში.',
                'keywords' => ['SafeTech Georgia', 'უსაფრთხოების სისტემების კომპანია', 'IT კომპანია საქართველო', 'ქსელის სპეციალისტი'],
                'schema_type' => 'AboutPage',
            ],
            'services' => [
                'slug' => '/services',
                'title' => 'კამერების მონტაჟი, ქსელის გაყვანა და IT სერვისები | SafeTech',
                'description' => 'SafeTech-ის სერვისები: ვიდეოსამეთვალყურეობა, კამერების მონტაჟი, LAN/Wi‑Fi ქსელები, დაშვების კონტროლი, შლაგბაუმები, სიგნალიზაცია, სერვერები, POS და IT მხარდაჭერა.',
                'keywords' => ['კამერების მონტაჟი თბილისი', 'ვიდეოსამეთვალყურეობა', 'ქსელის მონტაჟი', 'LAN კაბელის გაყვანა', 'WiFi მონტაჟი', 'IT support'],
                'schema_type' => 'CollectionPage',
            ],
            'projects' => [
                'slug' => '/projects',
                'title' => 'შესრულებული კამერების, ქსელებისა და IT პროექტები | SafeTech Georgia',
                'description' => 'ნახეთ SafeTech-ის რეალური ნამუშევრები: უსაფრთხოების კამერები, PoE/NVR სისტემები, ქსელის გაყვანა, Wi‑Fi, დაშვების კონტროლი და IT ინფრასტრუქტურა საქართველოში.',
                'keywords' => ['კამერების მონტაჟის პროექტები', 'ვიდეოსამეთვალყურეობის მონტაჟი', 'ქსელის პროექტები', 'SafeTech პროექტები'],
                'schema_type' => 'CollectionPage',
            ],
            'contact' => [
                'slug' => '/contact',
                'title' => 'კამერების მონტაჟის ფასი და უფასო კონსულტაცია | SafeTech',
                'description' => 'მიიღეთ SafeTech-ის კონსულტაცია და ინდივიდუალური შეთავაზება კამერების, ქსელის, Wi‑Fi, დაშვების კონტროლის, შლაგბაუმის, POS ან IT მომსახურებისთვის. 571 430 169 / 557 316 310.',
                'keywords' => ['კამერების მონტაჟის ფასი', 'უსაფრთხოების კამერების ფასი', 'IT მომსახურების ფასი', 'SafeTech კონტაქტი'],
                'schema_type' => 'ContactPage',
            ],
        ];

        foreach ($pages as $key => $page) {
            DB::table('seo_pages')->updateOrInsert(
                ['key' => $key],
                [
                    'slug' => $page['slug'],
                    'title' => $page['title'],
                    'description' => $page['description'],
                    'keywords' => json_encode($page['keywords'], JSON_UNESCAPED_UNICODE),
                    'og_title' => $page['title'],
                    'og_description' => $page['description'],
                    'schema_type' => $page['schema_type'],
                    'noindex' => false,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    private function refreshCorePageCopy(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $setting = DB::table('site_settings')->where('key', 'translations')->first();

        if (! $setting) {
            return;
        }

        $value = is_string($setting->value)
            ? json_decode($setting->value, true)
            : (array) $setting->value;
        $entries = is_array($value['entries'] ?? null) ? $value['entries'] : [];

        $copy = [
            'home.hero.eyebrow' => [
                'ka' => 'კამერები • ქსელები • IT მომსახურება • უსაფრთხოების სისტემები',
                'en' => 'CCTV • Networking • IT Services • Security Systems',
                'ru' => 'Камеры • Сети • IT-услуги • Системы безопасности',
            ],
            'home.hero.titlePrefix' => [
                'ka' => 'IT მომსახურება და უსაფრთხოების სისტემები',
                'en' => 'IT services and security systems',
                'ru' => 'IT-услуги и системы безопасности',
            ],
            'home.hero.titleAccent' => [
                'ka' => 'თქვენი ობიექტისთვის',
                'en' => 'for your property',
                'ru' => 'для вашего объекта',
            ],
            'home.hero.description' => [
                'ka' => 'ვგეგმავთ და ვამონტაჟებთ უსაფრთხოების კამერებს, LAN/Wi‑Fi ქსელებს, დაშვების კონტროლს, შლაგბაუმებს, POS სისტემებს და IT ინფრასტრუქტურას კერძო და ბიზნეს ობიექტებისთვის თბილისში და რეგიონებში.',
                'en' => 'We design and install CCTV, LAN/Wi‑Fi networks, access control, barrier gates, POS systems and IT infrastructure for homes and businesses in Tbilisi and across Georgia.',
                'ru' => 'Проектируем и устанавливаем видеонаблюдение, LAN/Wi‑Fi сети, контроль доступа, шлагбаумы, POS и IT-инфраструктуру для частных и коммерческих объектов в Тбилиси и регионах.',
            ],
            'home.hero.primaryCta' => [
                'ka' => 'მიიღეთ უფასო კონსულტაცია',
                'en' => 'Get a free consultation',
                'ru' => 'Получить бесплатную консультацию',
            ],
            'home.hero.secondaryCta' => [
                'ka' => 'ნახეთ სერვისები და ფასები',
                'en' => 'View services and pricing',
                'ru' => 'Услуги и цены',
            ],
            'services.hero.eyebrow' => [
                'ka' => 'კამერები • ქსელები • IT • ავტომატიზაცია',
                'en' => 'CCTV • Networking • IT • Automation',
                'ru' => 'Камеры • Сети • IT • Автоматизация',
            ],
            'services.hero.titlePrefix' => [
                'ka' => 'პროფესიონალური ტექნიკური სერვისები',
                'en' => 'Professional technical services',
                'ru' => 'Профессиональные технические услуги',
            ],
            'services.hero.titleAccent' => [
                'ka' => 'ერთი გუნდისგან',
                'en' => 'from one team',
                'ru' => 'от одной команды',
            ],
            'services.hero.titleSuffix' => [
                'ka' => 'დაგეგმვიდან მონტაჟსა და მხარდაჭერამდე',
                'en' => 'from planning to installation and support',
                'ru' => 'от проектирования до монтажа и поддержки',
            ],
            'services.hero.description' => [
                'ka' => 'აირჩიეთ საჭირო მომსახურება — კამერების მონტაჟი, ქსელის გაყვანა, Wi‑Fi, IT მხარდაჭერა, დაშვების კონტროლი, შლაგბაუმები, POS, სერვერები და სხვა ინფრასტრუქტურული სამუშაოები.',
                'en' => 'Choose the service you need: CCTV installation, network cabling, Wi‑Fi, IT support, access control, barrier gates, POS, servers and more.',
                'ru' => 'Выберите нужную услугу: монтаж камер, прокладка сети, Wi‑Fi, IT-поддержка, контроль доступа, шлагбаумы, POS, серверы и другое.',
            ],
            'about.hero.title' => [
                'ka' => 'SafeTech — ტექნიკური პარტნიორი უსაფრთხოებისა და IT ინფრასტრუქტურისთვის',
                'en' => 'SafeTech — your technical partner for security and IT infrastructure',
                'ru' => 'SafeTech — технический партнер по безопасности и IT-инфраструктуре',
            ],
            'about.hero.description' => [
                'ka' => 'ვაერთიანებთ ობიექტის შეფასებას, სწორ დაგეგმვას, პროფესიონალურ მონტაჟს, კონფიგურაციას, ტესტირებას და შემდგომ ტექნიკურ მხარდაჭერას.',
                'en' => 'We combine site assessment, planning, professional installation, configuration, testing and ongoing technical support.',
                'ru' => 'Объединяем обследование объекта, проектирование, профессиональный монтаж, настройку, тестирование и техническую поддержку.',
            ],
            'projects.hero.eyebrow' => [
                'ka' => 'რეალური ნამუშევრები და შედეგები',
                'en' => 'Real work and results',
                'ru' => 'Реальные работы и результаты',
            ],
            'projects.hero.title' => [
                'ka' => 'SafeTech-ის შესრულებული კამერების, ქსელებისა და IT პროექტები',
                'en' => 'Completed SafeTech CCTV, network and IT projects',
                'ru' => 'Реализованные проекты SafeTech по камерам, сетям и IT',
            ],
            'projects.hero.description' => [
                'ka' => 'ნახეთ როგორ ვგეგმავთ, ვამონტაჟებთ და ვაბარებთ რეალურ ობიექტებს — გამოყენებული მოწყობილობებით, სამუშაო პროცესით და საბოლოო შედეგით.',
                'en' => 'See how we plan, install and deliver real projects, including equipment, installation process and final results.',
                'ru' => 'Посмотрите, как мы проектируем, монтируем и сдаем реальные объекты: оборудование, процесс и итоговый результат.',
            ],
            'contact.hero.title' => [
                'ka' => 'მიიღეთ კონსულტაცია და შეთავაზება თქვენი ობიექტისთვის',
                'en' => 'Get a consultation and quote for your property',
                'ru' => 'Получите консультацию и расчет для вашего объекта',
            ],
            'contact.hero.description' => [
                'ka' => 'მოგვწერეთ რა გჭირდებათ და სად მდებარეობს ობიექტი. დაგეხმარებით კამერების, ქსელის, Wi‑Fi, დაშვების კონტროლის, შლაგბაუმის, POS ან IT მომსახურების სწორად დაგეგმვაში. 571 430 169 / 557 316 310.',
                'en' => 'Tell us what you need and where the property is located. We will help plan CCTV, networking, Wi‑Fi, access control, barrier gates, POS or IT services. 571 430 169 / 557 316 310.',
                'ru' => 'Напишите, что вам нужно и где находится объект. Поможем спланировать камеры, сеть, Wi‑Fi, контроль доступа, шлагбаум, POS или IT-услуги. 571 430 169 / 557 316 310.',
            ],
            'contact.hero.button' => [
                'ka' => 'მოითხოვეთ შეთავაზება',
                'en' => 'Request a quote',
                'ru' => 'Запросить расчет',
            ],
        ];

        foreach ($copy as $key => $translations) {
            $found = false;

            foreach ($entries as &$entry) {
                if (($entry['key'] ?? null) !== $key) {
                    continue;
                }

                $entry = array_merge($entry, ['key' => $key], $translations);
                $found = true;
                break;
            }
            unset($entry);

            if (! $found) {
                $entries[] = array_merge(['key' => $key], $translations);
            }
        }

        $value['entries'] = $entries;

        DB::table('site_settings')
            ->where('key', 'translations')
            ->update([
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Content migrations are intentionally not reversed so administrator
        // edits made after deployment are never overwritten by a rollback.
    }
};
