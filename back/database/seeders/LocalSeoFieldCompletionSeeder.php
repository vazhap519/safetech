<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Complete missing editable fields on EXISTING, verified Local SEO landings.
 *
 * This is a fill-only backfill, not a city-page factory: the city's genuine
 * service area and the administrator's copy, projects, media and indexing
 * decisions are never inferred or rewritten.
 */
final class LocalSeoFieldCompletionSeeder extends Seeder
{
    private const CITIES = [
        'tbilisi' => ['ka' => 'თბილისი', 'ka_in' => 'თბილისში', 'en' => 'Tbilisi', 'ru' => 'Тбилиси'],
        'bakuriani' => ['ka' => 'ბაკურიანი', 'ka_in' => 'ბაკურიანში', 'en' => 'Bakuriani', 'ru' => 'Бакуриани'],
        'surami' => ['ka' => 'სურამი', 'ka_in' => 'სურამში', 'en' => 'Surami', 'ru' => 'Сурами'],
        'borjomi' => ['ka' => 'ბორჯომი', 'ka_in' => 'ბორჯომში', 'en' => 'Borjomi', 'ru' => 'Боржоми'],
        'khashuri' => ['ka' => 'ხაშური', 'ka_in' => 'ხაშურში', 'en' => 'Khashuri', 'ru' => 'Хашури'],
        // Abastumani is a priority, but no page is created until its actual
        // service coverage and useful local content have been verified.
        'abastumani' => ['ka' => 'აბასთუმანი', 'ka_in' => 'აბასთუმანში', 'en' => 'Abastumani', 'ru' => 'Абастумани'],
    ];

    public function run(): void
    {
        LocalServiceLanding::query()->with('service')->chunkById(100, function ($landings): void {
            foreach ($landings as $landing) {
                $city = self::CITIES[$landing->location_slug] ?? null;
                $service = $landing->service;

                if (! $city || ! $service || ! filled($service->name)) {
                    continue;
                }

                $names = ['ka' => trim($service->name)];
                $descriptions = ['ka' => trim((string) $service->description)];

                foreach (['en', 'ru'] as $locale) {
                    $names[$locale] = trim((string) data_get($service->translations, "fields.name.{$locale}"));
                    $descriptions[$locale] = trim((string) data_get($service->translations, "fields.description.{$locale}"));
                }

                // Do not publish Georgian text masquerading as English/Russian.
                if (in_array('', $names, true) || in_array('', $descriptions, true)) {
                    continue;
                }

                $titles = [
                    'ka' => "{$names['ka']} {$city['ka_in']}",
                    'en' => "{$names['en']} in {$city['en']}",
                    'ru' => "{$names['ru']} в {$city['ru']}",
                ];
                $intro = [
                    'ka' => "{$titles['ka']}. {$descriptions['ka']}",
                    'en' => "{$titles['en']}. {$descriptions['en']}",
                    'ru' => "{$titles['ru']}. {$descriptions['ru']}",
                ];
                $context = [
                    'ka' => "მოგვწერეთ {$city['ka_in']} მდებარე ობიექტის ტიპი, არსებული ინფრასტრუქტურა და რა შედეგი გჭირდებათ. სამუშაოს მოცულობა, საჭირო მოწყობილობები და ფასი დაზუსტდება ობიექტის მოთხოვნების განხილვის შემდეგ.",
                    'en' => "Tell us about the property in {$city['en']}, existing infrastructure and the result you need. Scope, suitable equipment and price are confirmed after discussing the specific requirements.",
                    'ru' => "Расскажите об объекте в {$city['ru']}, существующей инфраструктуре и нужном результате. Объём работ, оборудование и стоимость уточняются после обсуждения задач объекта.",
                ];

                $fields = [
                    'location_name' => $city['ka'],
                    'eyebrow' => "{$names['ka']} · {$city['ka']}",
                    'title' => $titles['ka'],
                    'excerpt' => $intro['ka'],
                    'content' => "{$intro['ka']}\n\n{$context['ka']}",
                    'cta_title' => "მოითხოვეთ შეთავაზება — {$names['ka']}",
                    'cta_text' => $context['ka'],
                    'primary_keyword' => $titles['ka'],
                    'seo_title' => $titles['ka'].' | SafeTech',
                    'seo_description' => mb_substr($intro['ka'].' SafeTech — დაგვიკავშირდით დეტალების დასაზუსტებლად.', 0, 310),
                ];
                foreach ($fields as $field => $value) {
                    if (blank($landing->{$field})) {
                        $landing->{$field} = $value;
                    }
                }

                $translations = is_array($landing->translations) ? $landing->translations : [];
                $localCopy = [];
                foreach (['ka', 'en', 'ru'] as $locale) {
                    $localCopy[$locale] = [
                        'locationName' => $locale === 'ka' ? $city['ka'] : $city[$locale],
                        'eyebrow' => "{$names[$locale]} · ".($locale === 'ka' ? $city['ka'] : $city[$locale]),
                        'title' => $titles[$locale],
                        'excerpt' => $intro[$locale],
                        'content' => $intro[$locale]."\n\n".$context[$locale],
                        'ctaTitle' => match ($locale) {
                            'ka' => "მოითხოვეთ შეთავაზება — {$names[$locale]}",
                            'en' => "Request a quote for {$names[$locale]}",
                            'ru' => "Запросить предложение: {$names[$locale]}",
                        },
                        'ctaText' => $context[$locale],
                        'primaryKeyword' => $titles[$locale],
                        'seoTitle' => $titles[$locale].' | SafeTech',
                        'seoDescription' => mb_substr($intro[$locale], 0, 310),
                        'ogTitle' => $titles[$locale].' | SafeTech',
                        'ogDescription' => mb_substr($intro[$locale], 0, 310),
                    ];
                }

                // For KA, the database columns are authoritative: never
                // resurrect older boilerplate over edited CMS copy.
                foreach ([
                    'locationName' => 'location_name', 'eyebrow' => 'eyebrow',
                    'title' => 'title', 'excerpt' => 'excerpt', 'content' => 'content',
                    'ctaTitle' => 'cta_title', 'ctaText' => 'cta_text',
                    'primaryKeyword' => 'primary_keyword', 'seoTitle' => 'seo_title',
                    'seoDescription' => 'seo_description',
                ] as $translationField => $column) {
                    $localCopy['ka'][$translationField] = (string) $landing->{$column};
                }
                $localCopy['ka']['ogTitle'] = $localCopy['ka']['seoTitle'];
                $localCopy['ka']['ogDescription'] = $localCopy['ka']['seoDescription'];

                foreach ($localCopy as $locale => $copy) {
                    // Secondary-language fields are independent editorial
                    // values and are never replaced once filled.
                    foreach ($copy as $field => $value) {
                        $path = "fields.{$field}.{$locale}";
                        if (blank(data_get($translations, $path))) {
                            data_set($translations, $path, $value);
                        }
                    }

                    $path = "keywords.{$locale}";
                    $keywords = $locale === 'ka' ? $landing->keywords : data_get($translations, $path);
                    if (! is_array($keywords) || $keywords === []) {
                        $keywords = [$titles[$locale], $names[$locale].' '.($locale === 'ka' ? $city['ka'] : $city[$locale])];
                        if ($locale === 'ka') {
                            $landing->keywords = $keywords;
                        } else {
                            data_set($translations, $path, $keywords);
                        }
                    }
                }

                if (blank(data_get($translations, 'seo.schema_type'))) {
                    data_set($translations, 'seo.schema_type', 'Service');
                }

                // Only supply new multilingual blocks where none exist.
                // Translating a custom CMS item without understanding it is
                // unsafe; legacy authored rows have a separate exact-match
                // translation seeder and remain untouched here.
                if (empty($landing->benefits)) {
                    $landing->benefits = $this->benefits($names, $city);
                }
                if (empty($landing->faq)) {
                    $landing->faq = $this->faqs($names, $city);
                }

                if ($landing->translations !== $translations || $landing->isDirty()) {
                    $landing->translations = $translations;
                    $landing->save();
                }
            }
        });
    }

    /** @return array<int, array<string, mixed>> */
    private function benefits(array $names, array $city): array
    {
        return [
            $this->item(
                ['ka' => 'ობიექტზე მორგებული დაგეგმვა', 'en' => 'Site-specific planning', 'ru' => 'Планирование под объект'],
                [
                    'ka' => "{$names['ka']} — ჯერ ვაზუსტებთ {$city['ka_in']} მდებარე ობიექტის ამოცანას, პირობებსა და არსებულ სისტემას.",
                    'en' => "We discuss the requirements, conditions and existing setup for {$names['en']} in {$city['en']}.",
                    'ru' => "Уточняем задачи, условия и существующую систему для {$names['ru']} в {$city['ru']}.",
                ]
            ),
            $this->item(
                ['ka' => 'გასაგები სამუშაო მოცულობა', 'en' => 'Clear work scope', 'ru' => 'Понятный объём работ'],
                [
                    'ka' => 'საჭირო მასალები, სამუშაოს ეტაპები და ღირებულება თანხმდება რეალური მოთხოვნების მიხედვით.',
                    'en' => 'Materials, work stages and pricing are agreed according to the actual requirements.',
                    'ru' => 'Материалы, этапы работ и стоимость согласуются по реальным требованиям.',
                ]
            ),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function faqs(array $names, array $city): array
    {
        return [
            $this->item(
                [
                    'ka' => "როგორ ითვლება {$names['ka']} {$city['ka_in']}?",
                    'en' => "How is {$names['en']} priced in {$city['en']}?",
                    'ru' => "Как рассчитывается стоимость {$names['ru']} в {$city['ru']}?",
                ],
                [
                    'ka' => 'ღირებულება დამოკიდებულია ობიექტზე, სამუშაოს მოცულობაზე და საჭირო მოწყობილობებზე. მოგვაწოდეთ მოთხოვნები ინდივიდუალური შეთავაზებისთვის.',
                    'en' => 'Cost depends on the site, work scope and required equipment. Send your requirements for an individual quote.',
                    'ru' => 'Стоимость зависит от объекта, объёма работ и оборудования. Пришлите требования для индивидуального расчёта.',
                ],
                'question',
                'answer'
            ),
            $this->item(
                [
                    'ka' => 'რა ინფორმაციაა საჭირო შეთავაზებისთვის?',
                    'en' => 'What information is needed for a quote?',
                    'ru' => 'Что нужно для расчёта?',
                ],
                [
                    'ka' => "მოგვწერეთ ობიექტის ტიპი და მდებარეობა {$city['ka_in']}, არსებული მოწყობილობები და რა გსურთ რომ შეიცვალოს ან დამონტაჟდეს.",
                    'en' => "Share the property type and location in {$city['en']}, existing equipment and the change or installation you need.",
                    'ru' => "Сообщите тип и расположение объекта в {$city['ru']}, имеющееся оборудование и нужную настройку или монтаж.",
                ],
                'question',
                'answer'
            ),
        ];
    }

    private function item(array $titles, array $descriptions, string $titleKey = 'title', string $descriptionKey = 'description'): array
    {
        return [
            $titleKey => $titles['ka'],
            $descriptionKey => $descriptions['ka'],
            'translations' => [
                'en' => [$titleKey => $titles['en'], $descriptionKey => $descriptions['en']],
                'ru' => [$titleKey => $titles['ru'], $descriptionKey => $descriptions['ru']],
            ],
        ];
    }
}
