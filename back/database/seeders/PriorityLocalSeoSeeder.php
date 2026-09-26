<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Prepare Local SEO pages for every existing SafeTech service across six
 * confirmed priority areas, using the real service catalogue and distinct
 * city/category editorial briefs rather than city-name substitution alone.
 *
 * Short/noindexed catalogue services remain draft/noindex until their content
 * and availability have been reviewed. Never auto-publish an admin draft.
 */
final class PriorityLocalSeoSeeder extends Seeder
{
    private const LOCALES = ['ka', 'en', 'ru'];

    public function run(): void
    {
        $cities = PriorityLocalSeoCopy::cities();
        $technical = PriorityLocalSeoCopy::technical();
        $allowedSlugs = array_unique(array_merge(
            ServiceCatalogSeeder::canonicalServiceSlugs(),
            GoogleBusinessServicesSeeder::canonicalServiceSlugs(),
        ));

        Service::query()
            ->publiclyVisible()
            ->whereIn('slug', $allowedSlugs)
            ->with(['category', 'faqs'])
            ->get()
            ->each(function (Service $service) use ($cities, $technical): void {
                $names = ['ka' => trim((string) $service->name)];
                $descriptions = ['ka' => trim((string) $service->description)];

                foreach (['en', 'ru'] as $locale) {
                    $names[$locale] = trim((string) data_get($service->translations, "fields.name.{$locale}"));
                    $descriptions[$locale] = trim((string) data_get($service->translations, "fields.description.{$locale}"));
                }

                // No Georgian fallback on /en or /ru and no guessed translation.
                if (in_array('', $names, true) || in_array('', $descriptions, true)) {
                    return;
                }

                $category = (string) ($service->category?->slug ?? '');
                if ($category === 'telecommunications-infrastructure') {
                    $category = 'telecommunications-contractor';
                }
                if (! isset($technical[$category])) {
                    return;
                }

                $serviceIndexable = ! (bool) data_get($service->seo, 'noindex', false);

                foreach (PriorityLocalSeoCopy::CITY_ORDER as $priority => $slug) {
                    if (LocalServiceLanding::query()
                        ->where('service_id', $service->getKey())
                        ->where('location_slug', $slug)
                        ->exists()) {
                        // Retain existing CMS text, indexation, translations,
                        // photos and all manually verified project links.
                        continue;
                    }

                    $city = $cities[$slug];
                    $copy = $this->copy($service, $city, $technical[$category], $category, $names, $descriptions);

                    LocalServiceLanding::query()->create([
                        'service_id' => $service->getKey(),
                        'location_slug' => $slug,
                        'location_name' => $city['name']['ka'],
                        'eyebrow' => $copy['ka']['eyebrow'],
                        'title' => $copy['ka']['title'],
                        'excerpt' => $copy['ka']['excerpt'],
                        'content' => $copy['ka']['content'],
                        'benefits' => $this->benefits($city, $technical[$category], $category, $names),
                        'faq' => $this->faqs($service, $city, $category, $names, $technical[$category]),
                        'cta_title' => $copy['ka']['ctaTitle'],
                        'cta_text' => $copy['ka']['ctaText'],
                        'primary_keyword' => $copy['ka']['primaryKeyword'],
                        'keywords' => $copy['ka']['keywords'],
                        'seo_title' => $copy['ka']['seoTitle'],
                        'seo_description' => $copy['ka']['seoDescription'],
                        'translations' => [
                            'fields' => [
                                'locationName' => $this->localeField($copy, 'locationName'),
                                'eyebrow' => $this->localeField($copy, 'eyebrow'),
                                'title' => $this->localeField($copy, 'title'),
                                'excerpt' => $this->localeField($copy, 'excerpt'),
                                'content' => $this->localeField($copy, 'content'),
                                'ctaTitle' => $this->localeField($copy, 'ctaTitle'),
                                'ctaText' => $this->localeField($copy, 'ctaText'),
                                'primaryKeyword' => $this->localeField($copy, 'primaryKeyword'),
                                'seoTitle' => $this->localeField($copy, 'seoTitle'),
                                'seoDescription' => $this->localeField($copy, 'seoDescription'),
                                'ogTitle' => $this->localeField($copy, 'ogTitle'),
                                'ogDescription' => $this->localeField($copy, 'ogDescription'),
                            ],
                            'keywords' => $this->localeField($copy, 'keywords'),
                            'seo' => ['schema_type' => 'Service'],
                        ],
                        // A GBP seed's short/noindexed service is NOT evidence
                        // of a ready local landing. Prepare it for the editor
                        // but exclude it from Google until substantive review.
                        'is_published' => $serviceIndexable,
                        'noindex' => ! $serviceIndexable,
                        'published_at' => $serviceIndexable ? now() : null,
                        'sort_order' => ($priority + 1) * 100 + $service->sort_order,
                    ]);
                }
            });
    }

    /** @return array<string, array<string, mixed>> */
    private function copy(Service $service, array $city, array $technical, string $category, array $names, array $descriptions): array
    {
        $copy = [];
        foreach (self::LOCALES as $locale) {
            $location = $city['in'][$locale];
            $name = $names[$locale];
            $isCameraService = $service->slug === 'security-camera-installation';
            $title = $isCameraService
                ? match ($locale) {
                    'ka' => "უსაფრთხოების კამერების დაყენება და მონტაჟი {$location}",
                    'en' => "Security Camera Installation and Setup {$location}",
                    default => "Установка и монтаж камер видеонаблюдения {$location}",
                }
                : $this->limit("{$name} {$location}", 245);
            $scenario = $city['use'][$category][$locale];
            $siteContext = $city['context'][$locale];
            $checklist = $technical[$locale];

            $section = match ($locale) {
                'ka' => [
                    "რაში დაგეხმარებათ ეს მომსახურება {$city['in'][$locale]}?",
                    'ამ ობიექტისთვის გასათვალისწინებელი საკითხები',
                    'როგორ მზადდება შეთავაზება',
                    "მოგვწერეთ {$city['in'][$locale]} მდებარე ობიექტის ტიპი, არსებული მოწყობილობები და თქვენი ამოცანა. შეთანხმებამდე დაგიზუსტებთ სამუშაოს მოცულობასა და ღირებულებას.",
                ],
                'en' => [
                    "What to consider for this service in {$city['name'][$locale]}",
                    'Technical checks before the work',
                    'Planning an individual quote',
                    "Tell us your property type in {$city['name'][$locale]}, current equipment and what needs to change. Scope and price are confirmed before work is agreed.",
                ],
                default => [
                    "Особенности услуги в городе {$city['name'][$locale]}",
                    'Техническая подготовка',
                    'Как получить индивидуальное предложение',
                    "Укажите тип объекта в {$city['name'][$locale]}, оборудование и нужный результат. Объём работ и стоимость согласуются заранее.",
                ],
            };

            $intro = "{$title}. {$descriptions[$locale]}";
            $body = implode("\n\n", [
                $intro,
                $section[0]."\n".$scenario,
                $section[1]."\n".$checklist,
                $section[2]."\n".$siteContext."\n".$section[3],
            ]);

            $seo = $this->limit("{$title}. {$scenario} SafeTech.", 310);
            $seoTitle = $this->limit("{$title} | SafeTech", 255);
            $keywords = $isCameraService
                ? match ($locale) {
                    'ka' => ["კამერების დაყენება {$location}", "კამერების მონტაჟი {$location}", $title],
                    'en' => ["security camera installation {$city['name'][$locale]}", "CCTV installation {$city['name'][$locale]}", $title],
                    default => ["установка камер {$city['name'][$locale]}", "монтаж камер {$city['name'][$locale]}", $title],
                }
                : [
                    $title,
                    $this->limit("{$name} {$city['name'][$locale]}", 245),
                ];

            $copy[$locale] = [
                'locationName' => $city['name'][$locale],
                'eyebrow' => $this->limit("{$name} · {$city['name'][$locale]}", 255),
                'title' => $title,
                'excerpt' => $this->limit("{$intro} {$scenario}", 450),
                'content' => $body,
                'ctaTitle' => match ($locale) {
                    'ka' => "მოითხოვეთ შეთავაზება — {$city['in'][$locale]}",
                    'en' => "Request a quote in {$city['name'][$locale]}",
                    default => "Запросить предложение в {$city['name'][$locale]}",
                },
                'ctaText' => $section[3],
                'primaryKeyword' => $keywords[0],
                'keywords' => $keywords,
                'seoTitle' => $seoTitle,
                'seoDescription' => $seo,
                'ogTitle' => $seoTitle,
                'ogDescription' => $seo,
            ];
        }

        return $copy;
    }

    /** @return array<string, mixed> */
    private function benefits(array $city, array $technical, string $category, array $names): array
    {
        return [
            $this->item(
                ['ka' => 'მომსახურება ობიექტის საჭიროებების მიხედვით', 'en' => 'Plan for the actual property', 'ru' => 'План под конкретный объект'],
                $city['use'][$category]
            ),
            $this->item(
                ['ka' => 'ტექნიკური პირობების წინასწარი შეფასება', 'en' => 'Technical checks before work', 'ru' => 'Проверка условий до работ'],
                $technical
            ),
            $this->item(
                ['ka' => 'შეთანხმებული სამუშაო მოცულობა', 'en' => 'Agreed scope and quote', 'ru' => 'Согласованный объём работ'],
                [
                    'ka' => "{$names['ka']} — სამუშაოს მოცულობა და ფასი განისაზღვრება ობიექტის რეალური მოთხოვნების მიხედვით.",
                    'en' => "The scope and price for {$names['en']} depend on the property's actual requirements.",
                    'ru' => "Объём и стоимость услуги «{$names['ru']}» зависят от реальных задач объекта.",
                ]
            ),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function faqs(Service $service, array $city, string $category, array $names, array $technical): array
    {
        $items = [
            $this->item(
                [
                    'ka' => "რა არის საჭირო {$names['ka']} {$city['in']['ka']} დასაგეგმად?",
                    'en' => "What is needed to plan {$names['en']} in {$city['name']['en']}?",
                    'ru' => "Что нужно для планирования услуги «{$names['ru']}» в {$city['name']['ru']}?",
                ],
                $city['use'][$category],
                'question',
                'answer',
            ),
            $this->item(
                [
                    'ka' => 'როგორ განისაზღვრება მონტაჟის ან გამართვის ღირებულება?',
                    'en' => 'How is installation or configuration priced?',
                    'ru' => 'Как определяется стоимость монтажа или настройки?',
                ],
                [
                    'ka' => $technical['ka'].' ზუსტი ფასი დგინდება ობიექტის, მასალებისა და მოცულობის განხილვის შემდეგ.',
                    'en' => $technical['en'].' Pricing follows the site, materials and agreed work scope.',
                    'ru' => $technical['ru'].' Цена зависит от объекта, материалов и согласованного объёма.',
                ],
                'question',
                'answer',
            ),
        ];

        // Add the service's verified, translated technical FAQ, not invented
        // city-specific completions, brands, guarantees or testimonials.
        foreach ($service->faqs->where('is_active', true)->take(2) as $faq) {
            $question = ['ka' => (string) $faq->question];
            $answer = ['ka' => (string) $faq->answer];

            foreach (['en', 'ru'] as $locale) {
                $question[$locale] = trim((string) data_get($faq->translations, "fields.question.{$locale}"));
                $answer[$locale] = trim((string) data_get($faq->translations, "fields.answer.{$locale}"));
            }
            if (! in_array('', $question, true) && ! in_array('', $answer, true)) {
                $items[] = $this->item($question, $answer, 'question', 'answer');
            }
        }

        return $items;
    }

    private function item(array $title, array $description, string $titleKey = 'title', string $descriptionKey = 'description'): array
    {
        return [
            $titleKey => $title['ka'],
            $descriptionKey => $description['ka'],
            'translations' => [
                'en' => [$titleKey => $title['en'], $descriptionKey => $description['en']],
                'ru' => [$titleKey => $title['ru'], $descriptionKey => $description['ru']],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function localeField(array $copy, string $field): array
    {
        return [
            'ka' => $copy['ka'][$field],
            'en' => $copy['en'][$field],
            'ru' => $copy['ru'][$field],
        ];
    }

    private function limit(string $text, int $max): string
    {
        return mb_substr(trim($text), 0, $max);
    }
}
