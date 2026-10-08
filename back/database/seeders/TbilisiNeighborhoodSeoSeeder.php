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

    /**
     * Neighbourhood-specific planning questions, not claims about completed work.
     * Copy remains a draft until an editor verifies the site and project evidence.
     */
    private const LOCAL_BRIEFS = [
        'varketili' => [
            'ka' => 'მრავალბინიანი კორპუსის საერთო შესასვლელი, მცირე მაღაზია ან სამეურნეო ობიექტი: დააზუსტეთ კვების წერტილები, კაბელის ტრასა და ქსელთან წვდომა.',
            'en' => 'For an apartment entrance, small shop or utility property, confirm power points, cable routing and network access.',
            'ru' => 'Для подъезда, небольшого магазина или хозяйственного объекта уточните питание, кабельные трассы и доступ к сети.',
        ],
        'samgori' => [
            'ka' => 'სავაჭრო ან სასაწყობე სივრცისთვის წინასწარ შეაფასეთ დატვირთვა, შიდა და გარე ზონები, მოწყობილობების დაცვა და მომსახურებისთვის მისადგომობა.',
            'en' => 'For a retail or storage property, assess operating patterns, indoor and outdoor zones, device protection and maintenance access.',
            'ru' => 'Для торгового или складского объекта оцените режим работы, внутренние и наружные зоны, защиту устройств и доступ для обслуживания.',
        ],
        'isani' => [
            'ka' => 'კორპუსის, ოფისისა თუ კომერციული ობიექტის შემთხვევაში დააზუსტეთ საერთო სივრცეებზე დაშვების წესები, საკაბელო არხები და სამუშაო საათები.',
            'en' => 'For apartments, offices and commercial premises, check shared-area permissions, cable ducts and working hours.',
            'ru' => 'Для жилого дома, офиса или коммерческого помещения уточните доступ в общие зоны, кабельные каналы и время работ.',
        ],
        'vazisubani' => [
            'ka' => 'საცხოვრებელ კორპუსსა და კერძო ობიექტზე წინასწარ შეამოწმეთ სადარბაზოს კომუნიკაციები, გარე მოწყობილობების კვება და ინტერნეტის ხელმისაწვდომობა.',
            'en' => 'For residential buildings and private properties, inspect entrance wiring, outdoor power supply and internet availability.',
            'ru' => 'Для жилых домов и частных объектов проверьте проводку подъезда, питание наружных устройств и доступность интернета.',
        ],
    ];

    private const SERVICE_BRIEFS = [
        'security-camera-installation' => [
            'ka' => 'განისაზღვროს ხედვის ზონები, ღამის განათება, PoE კვება, NVR არქივის მოცულობა და მობილურიდან უსაფრთხო წვდომა.',
            'en' => 'Plan camera coverage, night lighting, PoE power, NVR retention and secure mobile access.',
            'ru' => 'Спланируйте зоны обзора, ночное освещение, питание PoE, архив NVR и безопасный доступ с телефона.',
        ],
        'intercom-access-control-installation' => [
            'ka' => 'დაზუსტდეს კარების და აბონენტების რაოდენობა, ელექტროსაკეტის ტიპი, გასვლის ღილაკი, ავარიული გახსნა და კაბელების თავსებადობა.',
            'en' => 'Confirm door and subscriber counts, electric lock type, exit button, emergency egress and cable compatibility.',
            'ru' => 'Уточните количество дверей и абонентов, тип электрозамка, кнопку выхода, аварийный выход и совместимость кабелей.',
        ],
        'barrier-gate-installation' => [
            'ka' => 'შემოწმდეს გასავლელის სიგანე, ქვეითთა უსაფრთხოება, საძირკველი, 220V კვება, ფოტოსენსორები და GSM გახსნის მართვა.',
            'en' => 'Check driveway width, pedestrian safety, foundation, 220V supply, photocells and GSM opening control.',
            'ru' => 'Проверьте ширину проезда, безопасность пешеходов, фундамент, питание 220В, фотоэлементы и управление через GSM.',
        ],
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
                $local = self::LOCAL_BRIEFS[$areaSlug] ?? null;
                $technical = self::SERVICE_BRIEFS[$slug];
                $kaContent = "{$kaTitle}. ".($local['ka'] ?? 'ობიექტის პირობები უნდა დაზუსტდეს ადგილზე.')." {$technical['ka']} სამუშაოს მოცულობა და ღირებულება განისაზღვრება ობიექტის შეფასების შემდეგ. რედაქტორმა გამოქვეყნებამდე უნდა გადაამოწმოს ადგილობრივი ფაქტები და რეალური პროექტების ბმულები.";
                $enContent = "{$enTitle}. ".($local['en'] ?? 'Confirm the property requirements on site.')." {$technical['en']} Scope and pricing follow a site assessment. Editorial draft: verify local facts and real project links before publication.";
                $ruContent = "{$ruTitle}. ".($local['ru'] ?? 'Уточните условия на объекте.')." {$technical['ru']} Объём и цена определяются после оценки объекта. Черновик: проверьте местные факты и ссылки на реальные проекты.";

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
