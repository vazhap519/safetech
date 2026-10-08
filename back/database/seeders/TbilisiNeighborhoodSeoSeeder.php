<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Editorial queue for the first Tbilisi neighbourhood SEO rollout.
 *
 * Drafts are intentionally unpublished and noindexed. An editor must add
 * verified local details, project links and reviewed translations before launch.
 */
final class TbilisiNeighborhoodSeoSeeder extends Seeder
{
    private const NEIGHBORHOODS = [
        'varketili' => ['ვარკეთილი', 'Varketili', 'Варкетили', 'ვარკეთილში'],
        'samgori' => ['სამგორი', 'Samgori', 'Самгори', 'სამგორში'],
        'isani' => ['ისანი', 'Isani', 'Исани', 'ისანში'],
        'vazisubani' => ['ვაზისუბანი', 'Vazisubani', 'Вазисубани', 'ვაზისუბანში'],
        'gldani' => ['გლდანი', 'Gldani', 'Глдани', 'გლდანში'],
        'saburtalo' => ['საბურთალო', 'Saburtalo', 'Сабуртало', 'საბურთალოზე'],
        'didi-dighomi' => ['დიდი დიღომი', 'Didi Dighomi', 'Большой Дигоми', 'დიდ დიღომში'],
        'dighomi' => ['დიღომი', 'Dighomi', 'Дигоми', 'დიღომში'],
    ];

    private const SERVICES = [
        'security-camera-installation' => ['ვიდეოკამერების მონტაჟი', 'Security camera installation', 'Установка камер видеонаблюдения'],
        'intercom-access-control-installation' => ['დომოფონებისა და დაშვების კონტროლის მონტაჟი', 'Intercom and access control installation', 'Установка домофонов и контроля доступа'],
        'barrier-gate-installation' => ['შლაგბაუმების მონტაჟი', 'Barrier gate installation', 'Установка шлагбаумов'],
    ];

    public function run(): void
    {
        foreach (self::SERVICES as $slug => [$kaService, $enService, $ruService]) {
            $service = Service::query()->where('slug', $slug)->first();
            if (! $service) {
                continue;
            }

            foreach (self::NEIGHBORHOODS as $areaSlug => [$kaArea, $enArea, $ruArea, $kaIn]) {
                if (LocalServiceLanding::query()
                    ->where('service_id', $service->getKey())
                    ->where('location_slug', $areaSlug)
                    ->exists()) {
                    // Never overwrite CMS edits, publication state or project associations.
                    continue;
                }

                $kaTitle = "{$kaService} {$kaIn}";
                $enTitle = "{$enService} in {$enArea}";
                $ruTitle = "{$ruService} в районе {$ruArea}";
                $kaContent = "{$kaTitle}. ეს არის რედაქტორის სამუშაო ვერსია. გამოქვეყნებამდე საჭიროა {$kaArea} მდებარე ობიექტების ტიპების, მონტაჟის პირობების, რეალური სამუშაოების და შესაბამისი პროექტების გადამოწმება.";
                $enContent = "{$enTitle}. Editorial draft: verify property types, installation constraints and relevant completed projects in {$enArea} before publishing.";
                $ruContent = "{$ruTitle}. Черновик редактора: перед публикацией проверьте типы объектов, условия монтажа и реальные проекты в районе {$ruArea}.";

                LocalServiceLanding::query()->create([
                    'service_id' => $service->getKey(),
                    'location_slug' => $areaSlug,
                    'location_name' => $kaArea,
                    'title' => $kaTitle,
                    'excerpt' => $kaContent,
                    'content' => $kaContent,
                    'primary_keyword' => $kaTitle,
                    'keywords' => [$kaTitle],
                    'seo_title' => "{$kaTitle} | SafeTech",
                    'seo_description' => $kaContent,
                    'translations' => [
                        'fields' => [
                            'locationName' => ['ka' => $kaArea, 'en' => $enArea, 'ru' => $ruArea],
                            'title' => ['ka' => $kaTitle, 'en' => $enTitle, 'ru' => $ruTitle],
                            'excerpt' => ['ka' => $kaContent, 'en' => $enContent, 'ru' => $ruContent],
                            'content' => ['ka' => $kaContent, 'en' => $enContent, 'ru' => $ruContent],
                            'primaryKeyword' => ['ka' => $kaTitle, 'en' => $enTitle, 'ru' => $ruTitle],
                            'seoTitle' => ['ka' => "{$kaTitle} | SafeTech", 'en' => "{$enTitle} | SafeTech", 'ru' => "{$ruTitle} | SafeTech"],
                            'seoDescription' => ['ka' => $kaContent, 'en' => $enContent, 'ru' => $ruContent],
                        ],
                        'keywords' => ['ka' => [$kaTitle], 'en' => [$enTitle], 'ru' => [$ruTitle]],
                    ],
                    'is_published' => false,
                    'noindex' => true,
                    'published_at' => null,
                    'sort_order' => 20000 + array_search($areaSlug, array_keys(self::NEIGHBORHOODS), true) * 100,
                ]);
            }
        }
    }
}
