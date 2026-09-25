<?php

use App\Models\SeoPage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const OLD = [
        'title' => 'უსაფრთხოების სისტემები და IT მომსახურება საქართველოში | SafeTech',
        'description' => 'SafeTech კერძო პირებსა და ბიზნესებს სთავაზობს უსაფრთხოების სისტემების, ვიდეომეთვალყურეობის, ქსელური ინფრასტრუქტურისა და IT გადაწყვეტილებების დაგეგმვას, მონტაჟს, კონფიგურაციასა და ტექნიკურ მხარდაჭერას თბილისში და საქართველოს რეგიონებში. მიიღეთ კონსულტაცია და მოითხოვეთ შეთავაზება.',
        'og_title' => 'კამერები, ქსელები და IT გადაწყვეტილებები თქვენი ობიექტისთვის | SafeTech',
        'og_description' => 'უსაფრთხოების კამერების მონტაჟი, დაცვისა და განგაშის სისტემები, ქსელის მოწყობა, Wi-Fi, წვდომის კონტროლი და IT მხარდაჭერა თბილისში და საქართველოს რეგიონებში. დაგვიკავშირდით კონსულტაციისთვის.',
        'localized' => [
            'title' => [
                'ka' => 'უსაფრთხოების სისტემები და IT მომსახურება საქართველოში | SafeTech',
                'en' => 'Security Systems and IT Services in Georgia | SafeTech',
                'ru' => 'Системы безопасности и IT-услуги в Грузии | SafeTech',
            ],
            'description' => [
                'ka' => 'SafeTech კერძო პირებსა და ბიზნესებს სთავაზობს უსაფრთხოების სისტემების, ვიდეომეთვალყურეობის, ქსელური ინფრასტრუქტურისა და IT გადაწყვეტილებების დაგეგმვას, მონტაჟს, კონფიგურაციასა და ტექნიკურ მხარდაჭერას თბილისში და საქართველოს რეგიონებში. მიიღეთ კონსულტაცია და მოითხოვეთ შეთავაზება.',
                'en' => 'SafeTech provides individuals and businesses in Tbilisi and across Georgia with planning, installation, configuration and technical support for security systems, video surveillance, network infrastructure and IT solutions. Get a consultation and request a proposal.',
                'ru' => 'SafeTech предлагает частным лицам и бизнесу в Тбилиси и регионах Грузии планирование, монтаж, настройку и техническую поддержку систем безопасности, видеонаблюдения, сетевой инфраструктуры и IT-решений. Получите консультацию и запросите предложение.',
            ],
            'og_title' => [
                'ka' => 'კამერები, ქსელები და IT გადაწყვეტილებები თქვენი ობიექტისთვის | SafeTech',
                'en' => 'Cameras, Networks and IT Solutions for Your Property | SafeTech',
                'ru' => 'Камеры, сети и IT-решения для вашего объекта | SafeTech',
            ],
            'og_description' => [
                'ka' => 'უსაფრთხოების კამერების მონტაჟი, დაცვისა და განგაშის სისტემები, ქსელის მოწყობა, Wi-Fi, წვდომის კონტროლი და IT მხარდაჭერა თბილისში და საქართველოს რეგიონებში. დაგვიკავშირდით კონსულტაციისთვის.',
                'en' => 'Security camera installation, alarm systems, network installation, Wi-Fi configuration, access control and IT support in Tbilisi and across Georgia. Contact SafeTech for a consultation.',
                'ru' => 'Установка камер видеонаблюдения, систем охраны и сигнализации, монтаж локальной сети, настройка Wi-Fi, контроль доступа и IT-поддержка в Тбилиси и регионах Грузии. Свяжитесь с SafeTech для консультации.',
            ],
        ],
    ];

    private const NEW = [
        'title' => 'უსაფრთხოების სისტემები და IT მომსახურება | SafeTech',
        'description' => 'უსაფრთხოების კამერები, ქსელები, Wi‑Fi და IT მომსახურება თბილისში და რეგიონებში. SafeTech გეგმავს და ამონტაჟებს სისტემებს. მოითხოვეთ კონსულტაცია.',
        'og_title' => 'უსაფრთხოების სისტემები და IT მომსახურება | SafeTech',
        'og_description' => 'უსაფრთხოების კამერები, ქსელები, Wi‑Fi და IT მომსახურება თბილისში და რეგიონებში. SafeTech გეგმავს და ამონტაჟებს სისტემებს. მოითხოვეთ კონსულტაცია.',
        'localized' => [
            'title' => [
                'ka' => 'უსაფრთხოების სისტემები და IT მომსახურება | SafeTech',
                'en' => 'Security Systems & IT Services in Georgia | SafeTech',
                'ru' => 'Системы безопасности и IT-услуги в Грузии | SafeTech',
            ],
            'description' => [
                'ka' => 'უსაფრთხოების კამერები, ქსელები, Wi‑Fi და IT მომსახურება თბილისში და რეგიონებში. SafeTech გეგმავს და ამონტაჟებს სისტემებს. მოითხოვეთ კონსულტაცია.',
                'en' => 'CCTV, networking, Wi-Fi and IT support in Tbilisi and across Georgia. SafeTech plans and installs systems for homes and businesses. Contact us.',
                'ru' => 'Видеонаблюдение, сети, Wi-Fi и IT-поддержка в Тбилиси и по всей Грузии. SafeTech проектирует и устанавливает решения для дома и бизнеса. Свяжитесь с нами.',
            ],
            'og_title' => [
                'ka' => 'უსაფრთხოების სისტემები და IT მომსახურება | SafeTech',
                'en' => 'Security Systems & IT Services in Georgia | SafeTech',
                'ru' => 'Системы безопасности и IT-услуги в Грузии | SafeTech',
            ],
            'og_description' => [
                'ka' => 'უსაფრთხოების კამერები, ქსელები, Wi‑Fi და IT მომსახურება თბილისში და რეგიონებში. SafeTech გეგმავს და ამონტაჟებს სისტემებს. მოითხოვეთ კონსულტაცია.',
                'en' => 'CCTV, networking, Wi-Fi and IT support in Tbilisi and across Georgia. SafeTech plans and installs systems for homes and businesses. Contact us.',
                'ru' => 'Видеонаблюдение, сети, Wi-Fi и IT-поддержка в Тбилиси и по всей Грузии. SafeTech проектирует и устанавливает решения для дома и бизнеса. Свяжитесь с нами.',
            ],
        ],
    ];

    public function up(): void
    {
        $this->replaceHomeSeo(self::OLD, self::NEW);
    }

    public function down(): void
    {
        $this->replaceHomeSeo(self::NEW, self::OLD);
    }

    private function replaceHomeSeo(array $expected, array $replacement): void
    {
        $page = SeoPage::query()->where('key', 'home')->first();

        if (! $page) {
            return;
        }

        foreach (['title', 'description', 'og_title', 'og_description'] as $field) {
            if ($page->getAttribute($field) !== $expected[$field]) {
                return;
            }
        }

        $translations = is_array($page->translations) ? $page->translations : [];

        foreach ($expected['localized'] as $field => $locales) {
            foreach ($locales as $locale => $value) {
                if (data_get($translations, "fields.{$field}.{$locale}") !== $value) {
                    return;
                }
            }
        }

        foreach (['title', 'description', 'og_title', 'og_description'] as $field) {
            $page->setAttribute($field, $replacement[$field]);

            foreach ($replacement['localized'][$field] as $locale => $value) {
                data_set($translations, "fields.{$field}.{$locale}", $value);
            }
        }

        $page->translations = $translations;
        $page->save();
    }
};
