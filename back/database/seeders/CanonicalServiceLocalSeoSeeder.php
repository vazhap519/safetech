<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Database\Seeder;

/** Add localized Tbilisi coverage from verified canonical service copy, never fabricated projects. */
final class CanonicalServiceLocalSeoSeeder extends Seeder
{
    private const LOCATION = ['ka' => 'თბილისი', 'en' => 'Tbilisi', 'ru' => 'Тбилиси'];

    public function run(): void
    {
        $services = Service::query()->publiclyVisible()
            ->whereIn('slug', ServiceCatalogSeeder::canonicalServiceSlugs())->get();

        foreach ($services as $service) {
            // Preserve all CMS-managed copy, including deliberately unpublished pages.
            if (LocalServiceLanding::query()->where('service_id', $service->id)
                ->where('location_slug', 'tbilisi')->exists()) {
                continue;
            }

            $titles = $eyebrows = $excerpts = $contents = $seoTitles = [];
            $seoDescriptions = $ctaTitles = $ctaTexts = $keywords = [];

            foreach (['ka', 'en', 'ru'] as $locale) {
                $name = trim((string) data_get($service->translations, "fields.name.{$locale}"));
                $description = trim((string) data_get($service->translations, "fields.description.{$locale}"));
                $seo = trim((string) data_get($service->translations, "fields.seoDescription.{$locale}"));

                // Incomplete source translations must be resolved editorially.
                if ($name === '' || $description === '' || $seo === '') {
                    continue 2;
                }

                $titles[$locale] = match ($locale) {
                    'en' => "{$name} in Tbilisi",
                    'ru' => "{$name} в Тбилиси",
                    default => "{$name} თბილისში",
                };
                $eyebrows[$locale] = "{$name} · ".self::LOCATION[$locale];
                $excerpts[$locale] = $description;
                $contents[$locale] = implode("\n\n", [
                    "{$titles[$locale]}. {$description}",
                    $seo,
                    $this->requirementsCopy($locale),
                ]);
                $seoTitles[$locale] = "{$titles[$locale]} | SafeTech";
                $seoDescriptions[$locale] = match ($locale) {
                    'en' => "Tbilisi: {$seo}",
                    'ru' => "Тбилиси: {$seo}",
                    default => "თბილისი: {$seo}",
                };
                $ctaTitles[$locale] = match ($locale) {
                    'en' => "Request a {$name} quote",
                    'ru' => "Запросить предложение: {$name}",
                    default => "{$name} — მოითხოვეთ შეთავაზება",
                };
                $ctaTexts[$locale] = $this->quoteCopy($locale);
                $keywords[$locale] = [$titles[$locale], "{$name} ".self::LOCATION[$locale]];
            }

            LocalServiceLanding::query()->create([
                'service_id' => $service->id,
                'location_slug' => 'tbilisi',
                'location_name' => self::LOCATION['ka'],
                'eyebrow' => $eyebrows['ka'],
                'title' => $titles['ka'],
                'excerpt' => $excerpts['ka'],
                'content' => $contents['ka'],
                'cta_title' => $ctaTitles['ka'],
                'cta_text' => $ctaTexts['ka'],
                'primary_keyword' => $titles['ka'],
                'keywords' => $keywords['ka'],
                'seo_title' => $seoTitles['ka'],
                'seo_description' => $seoDescriptions['ka'],
                'translations' => [
                    'fields' => [
                        'locationName' => self::LOCATION,
                        'eyebrow' => $eyebrows,
                        'title' => $titles,
                        'excerpt' => $excerpts,
                        'content' => $contents,
                        'ctaTitle' => $ctaTitles,
                        'ctaText' => $ctaTexts,
                        'primaryKeyword' => $titles,
                        'seoTitle' => $seoTitles,
                        'seoDescription' => $seoDescriptions,
                        'ogTitle' => $seoTitles,
                        'ogDescription' => $seoDescriptions,
                    ],
                    'keywords' => $keywords,
                    'seo' => ['schema_type' => 'Service'],
                ],
                'is_published' => true,
                'noindex' => false,
                'published_at' => now(),
                'sort_order' => 102,
            ]);
        }
    }

    private function requirementsCopy(string $locale): string
    {
        return match ($locale) {
            'en' => 'Before work begins, we review the site conditions, existing equipment and requested outcome. We agree the scope, suitable materials and necessary checks for the specific job. The technical solution and pricing depend on the actual requirements and are confirmed after consultation.',
            'ru' => 'Перед началом работ уточняем условия объекта, имеющееся оборудование и нужный результат. Согласуем объём работ, подходящие материалы и необходимые проверки. Техническое решение и стоимость зависят от требований объекта и уточняются после консультации.',
            default => 'სამუშაოს დაწყებამდე ვაზუსტებთ ობიექტის პირობებს, არსებულ მოწყობილობებსა და სასურველ შედეგს. კონკრეტული სამუშაოსთვის შეთანხმდება მოცულობა, შესაბამისი მასალები და საჭირო შემოწმებები. ტექნიკური გადაწყვეტა და ღირებულება დამოკიდებულია რეალურ მოთხოვნებზე და ზუსტდება კონსულტაციის შემდეგ.',
        };
    }

    private function quoteCopy(string $locale): string
    {
        return match ($locale) {
            'en' => 'Share the Tbilisi site type, current setup, requirements and available equipment. We will clarify the scope and prepare a suitable proposal.',
            'ru' => 'Сообщите тип объекта в Тбилиси, текущее оснащение, задачи и имеющееся оборудование. Уточним объём работ и подготовим предложение.',
            default => 'მოგვწერეთ თბილისის ობიექტის ტიპი, არსებული მდგომარეობა, მოთხოვნები და ხელთ არსებული ტექნიკა. დავაზუსტებთ სამუშაოს მოცულობას და მოვამზადებთ შესაბამის შეთავაზებას.',
        };
    }
}
