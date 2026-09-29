<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Prepare trilingual Local SEO drafts for canonical services in verified
 * SafeTech target cities. A draft is NOT automatically an indexable landing:
 * editorial review and useful local evidence are required before publishing.
 */
final class PriorityCityLocalSeoSeeder extends Seeder
{
    private const LOCALES = ['ka', 'en', 'ru'];

    private const CITIES = [
        'tbilisi' => [
            'name' => ['ka' => 'თბილისი', 'en' => 'Tbilisi', 'ru' => 'Тбилиси'],
            'in' => ['ka' => 'თბილისში', 'en' => 'in Tbilisi', 'ru' => 'в Тбилиси'],
            'context' => [
                'ka' => 'საცხოვრებელი კორპუსის, ოფისის, მაღაზიის ან კერძო ობიექტის შემთხვევაში წინასწარ ვაზუსტებთ ქსელის, კვებისა და მონტაჟის პირობებს.',
                'en' => 'For apartment buildings, offices, shops and private properties, we first review the network, power and installation conditions.',
                'ru' => 'Для жилых домов, офисов, магазинов и частных объектов сначала уточняем условия сети, питания и монтажа.',
            ],
        ],
        'bakuriani' => [
            'name' => ['ka' => 'ბაკურიანი', 'en' => 'Bakuriani', 'ru' => 'Бакуриани'],
            'in' => ['ka' => 'ბაკურიანში', 'en' => 'in Bakuriani', 'ru' => 'в Бакуриани'],
            'context' => [
                'ka' => 'კოტეჯისა და სასტუმროსთვის მნიშვნელოვანია სეზონური დატვირთვის, დაბალი ტემპერატურის, ინტერნეტისა და ელექტროკვების პირობების წინასწარ შეფასება, თუ ისინი კონკრეტულ სამუშაოს ეხება.',
                'en' => 'For cottages and hotels, seasonal use, low temperatures, connectivity and power conditions are assessed where relevant to the requested work.',
                'ru' => 'Для коттеджей и гостиниц при необходимости учитываем сезонную нагрузку, низкие температуры, интернет и электропитание.',
            ],
        ],
        'surami' => [
            'name' => ['ka' => 'სურამი', 'en' => 'Surami', 'ru' => 'Сурами'],
            'in' => ['ka' => 'სურამში', 'en' => 'in Surami', 'ru' => 'в Сурами'],
            'context' => [
                'ka' => 'კერძო სახლის, აგარაკისა თუ მცირე საოჯახო სასტუმროსთვის გადაწყვეტა შეირჩევა ობიექტის გამოყენების რეჟიმისა და არსებული ინფრასტრუქტურის მიხედვით.',
                'en' => 'For homes, holiday houses and small guesthouses, the approach depends on how the property is used and its existing infrastructure.',
                'ru' => 'Для частных домов, дач и небольших гостевых домов решение зависит от режима использования и имеющейся инфраструктуры.',
            ],
        ],
        'borjomi' => [
            'name' => ['ka' => 'ბორჯომი', 'en' => 'Borjomi', 'ru' => 'Боржоми'],
            'in' => ['ka' => 'ბორჯომში', 'en' => 'in Borjomi', 'ru' => 'в Боржоми'],
            'context' => [
                'ka' => 'სასტუმროს, საოჯახო სასტუმროსა და საცხოვრებელი ობიექტისთვის ვაზუსტებთ სტუმრებისა და პერსონალის საჭიროებებს, თუ ეს კონკრეტული მომსახურებისთვის მნიშვნელოვანია.',
                'en' => 'For hotels, guesthouses and residential sites, we clarify guest and staff requirements when relevant to the service.',
                'ru' => 'Для гостиниц, гостевых домов и жилых объектов уточняем потребности гостей и персонала, если это важно для услуги.',
            ],
        ],
        'khashuri' => [
            'name' => ['ka' => 'ხაშური', 'en' => 'Khashuri', 'ru' => 'Хашури'],
            'in' => ['ka' => 'ხაშურში', 'en' => 'in Khashuri', 'ru' => 'в Хашури'],
            'context' => [
                'ka' => 'კერძო და კომერციული ობიექტების შემთხვევაში სამუშაოს მოცულობა განისაზღვრება არსებული მოწყობილობებით, კაბელებითა და მომხმარებლის მოთხოვნით.',
                'en' => 'For homes and commercial premises, the work scope depends on existing equipment, cabling and customer requirements.',
                'ru' => 'Для частных и коммерческих объектов объём работ зависит от оборудования, кабелей и требований заказчика.',
            ],
        ],
        'abastumani' => [
            'name' => ['ka' => 'აბასთუმანი', 'en' => 'Abastumani', 'ru' => 'Абастумани'],
            'in' => ['ka' => 'აბასთუმანში', 'en' => 'in Abastumani', 'ru' => 'в Абастумани'],
            'context' => [
                'ka' => 'სასტუმროსა და დასასვენებელი ობიექტისთვის წინასწარ განვიხილავთ სეზონურ გამოყენებას, კავშირის პირობებსა და მონტაჟის ხელმისაწვდომობას, როცა ეს სამუშაოს ეხება.',
                'en' => 'For hotels and holiday properties, we review seasonal use, connectivity and installation access when relevant.',
                'ru' => 'Для гостиниц и мест отдыха при необходимости учитываем сезонное использование, связь и доступ для монтажа.',
            ],
        ],
    ];

    public function run(): void
    {
        Service::query()->publiclyVisible()
            ->whereIn('slug', ServiceCatalogSeeder::canonicalServiceSlugs())
            ->chunkById(100, function ($services): void {
                foreach ($services as $service) {
                    foreach (self::CITIES as $slug => $city) {
                        if (LocalServiceLanding::query()
                            ->where('service_id', $service->getKey())
                            ->where('location_slug', $slug)->exists()) {
                            continue;
                        }

                        $copy = $this->copy($service, $city);
                        if ($copy === null) {
                            continue;
                        }

                        LocalServiceLanding::query()->create([
                            'service_id' => $service->getKey(),
                            'location_slug' => $slug,
                            'location_name' => $city['name']['ka'],
                            'eyebrow' => $copy['eyebrow']['ka'],
                            'title' => $copy['title']['ka'],
                            'excerpt' => $copy['excerpt']['ka'],
                            'content' => $copy['content']['ka'],
                            'cta_title' => $copy['ctaTitle']['ka'],
                            'cta_text' => $copy['ctaText']['ka'],
                            'primary_keyword' => $copy['primaryKeyword']['ka'],
                            'keywords' => $copy['keywords']['ka'],
                            'seo_title' => $copy['seoTitle']['ka'],
                            'seo_description' => $copy['seoDescription']['ka'],
                            'translations' => [
                                'fields' => [
                                    'locationName' => $city['name'],
                                    'eyebrow' => $copy['eyebrow'],
                                    'title' => $copy['title'],
                                    'excerpt' => $copy['excerpt'],
                                    'content' => $copy['content'],
                                    'ctaTitle' => $copy['ctaTitle'],
                                    'ctaText' => $copy['ctaText'],
                                    'primaryKeyword' => $copy['primaryKeyword'],
                                    'seoTitle' => $copy['seoTitle'],
                                    'seoDescription' => $copy['seoDescription'],
                                    'ogTitle' => $copy['seoTitle'],
                                    'ogDescription' => $copy['seoDescription'],
                                ],
                                'keywords' => $copy['keywords'],
                                'seo' => ['schema_type' => 'Service'],
                            ],
                            // Do not mass-publish near-identical city templates.
                            // Editors use the existing AI generator to expand,
                            // verify and approve each page before indexing.
                            'is_published' => false,
                            'noindex' => true,
                            'sort_order' => array_search($slug, array_keys(self::CITIES), true) + 200,
                        ]);
                    }
                }
            });
    }

    private function copy(Service $service, array $city): ?array
    {
        $copy = [
            'eyebrow' => [], 'title' => [], 'excerpt' => [], 'content' => [],
            'ctaTitle' => [], 'ctaText' => [], 'primaryKeyword' => [],
            'keywords' => [], 'seoTitle' => [], 'seoDescription' => [],
        ];

        foreach (self::LOCALES as $locale) {
            $name = trim((string) ($locale === 'ka'
                ? $service->name
                : data_get($service->translations, "fields.name.{$locale}")));
            $description = trim((string) ($locale === 'ka'
                ? $service->description
                : data_get($service->translations, "fields.description.{$locale}")));

            // Never pass untranslated Georgian placeholders off as EN/RU.
            if ($name === '' || $description === '') {
                return null;
            }

            $place = $city['in'][$locale];
            $copy['title'][$locale] = "{$name} {$place}";
            $copy['eyebrow'][$locale] = "{$name} · {$city['name'][$locale]}";
            $copy['excerpt'][$locale] = "{$copy['title'][$locale]}. {$description}";
            $copy['content'][$locale] = implode("\n\n", [
                $copy['excerpt'][$locale],
                $city['context'][$locale],
                match ($locale) {
                    'en' => 'Tell us the property type, existing equipment and desired result. The scope and price are confirmed after reviewing your requirements.',
                    'ru' => 'Укажите тип объекта, имеющееся оборудование и желаемый результат. Объём и стоимость уточняются после обсуждения требований.',
                    default => 'მოგვწერეთ ობიექტის ტიპი, არსებული მოწყობილობები და სასურველი შედეგი. სამუშაოს მოცულობა და ფასი დაზუსტდება მოთხოვნების განხილვის შემდეგ.',
                },
            ]);
            $copy['ctaTitle'][$locale] = match ($locale) {
                'en' => "Request a quote: {$name}",
                'ru' => "Запросить предложение: {$name}",
                default => "მოითხოვეთ შეთავაზება — {$name}",
            };
            $copy['ctaText'][$locale] = match ($locale) {
                'en' => "Share your {$city['name'][$locale]} property requirements for a tailored quote.",
                'ru' => "Опишите задачи объекта в {$city['name'][$locale]} для индивидуального расчёта.",
                default => "მოგვწერეთ {$city['in'][$locale]} მდებარე ობიექტის მოთხოვნები ინდივიდუალური შეთავაზებისთვის.",
            };
            $copy['primaryKeyword'][$locale] = $copy['title'][$locale];
            $copy['keywords'][$locale] = [
                $copy['title'][$locale],
                "{$name} {$city['name'][$locale]}",
            ];
            $copy['seoTitle'][$locale] = "{$copy['title'][$locale]} | SafeTech";
            $copy['seoDescription'][$locale] = mb_substr(
                "{$copy['title'][$locale]}. {$description} {$city['context'][$locale]}",
                0,
                310
            );
        }

        return $copy;
    }
}
