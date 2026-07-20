<?php

namespace Database\Seeders;

use App\Models\CategoryForService;
use App\Models\PrivacyPolicy;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\Calculators\DefaultCalculatorProfiles;
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
            } else {
                $current = is_array($setting->value) ? $setting->value : [];
                $value = array_replace_recursive($value, $current);
            }

            $setting->forceFill([
                'group' => 'general',
                'value' => $value,
                'is_public' => true,
            ])->save();
        }

        $this->seedPrivacyPolicy();

        if (! config('app.seed_demo_content')) {
            return;
        }

        $serviceCategories = collect($this->serviceCategories())
            ->mapWithKeys(function (array $category): array {
                $record = CategoryForService::query()->firstOrCreate(
                    ['slug' => $category['slug']],
                    $category,
                );

                $updates = [];

                foreach (['seo_title', 'seo_description', 'intro_text'] as $field) {
                    if (blank($record->getAttribute($field))) {
                        $updates[$field] = $category[$field];
                    }
                }

                $currentTranslations = is_array($record->translations)
                    ? $record->translations
                    : [];
                $mergedTranslations = array_replace_recursive(
                    $category['translations'],
                    $currentTranslations,
                );

                if ($mergedTranslations !== $currentTranslations) {
                    $updates['translations'] = $mergedTranslations;
                }

                if ($updates !== []) {
                    $record->forceFill($updates)->save();
                }

                return [$category['slug'] => $record];
            });

        $services = [
            [
                'slug' => 'cctv',
                'category_slug' => 'security-systems',
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
                'lead_form' => DefaultCalculatorProfiles::for('cctv'),
                'translations' => $this->serviceTranslations(
                    ['ვიდეოსამეთვალყურეობა', 'CCTV', 'Видеонаблюдение'],
                    ['ვიდეოსამეთვალყურეობის მონტაჟი და მონიტორინგი', 'CCTV installation and monitoring', 'Монтаж и мониторинг видеонаблюдения'],
                    [
                        'ოფისების, სავაჭრო სივრცეების, საწყობებისა და საცხოვრებელი ობიექტების პროფესიონალური ვიდეოსამეთვალყურეო სისტემები.',
                        'Professional camera systems for offices, retail, warehouses, and residential buildings.',
                        'Профессиональные системы видеонаблюдения для офисов, магазинов, складов и жилых объектов.',
                    ],
                ),
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'networking',
                'category_slug' => 'network-infrastructure',
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
                'lead_form' => DefaultCalculatorProfiles::for('networking'),
                'translations' => $this->serviceTranslations(
                    ['ქსელური ინფრასტრუქტურა', 'Network Infrastructure', 'Сетевая инфраструктура'],
                    ['ქსელური ინფრასტრუქტურა', 'Network Infrastructure', 'Сетевая инфраструктура'],
                    [
                        'სტრუქტურული კაბელირება, როუტერები, სვიჩები, Wi-Fi დაფარვა და დაცული ბიზნეს ქსელები.',
                        'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.',
                        'Структурированная кабельная система, роутеры, коммутаторы, Wi-Fi и защищенные бизнес-сети.',
                    ],
                ),
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'access-control',
                'category_slug' => 'security-systems',
                'name' => 'Access Control',
                'eyebrow' => 'Security systems',
                'icon' => 'fingerprint',
                'title' => 'Access control and attendance systems',
                'description' => 'Card, PIN, biometric, and face-recognition access control for secure workplaces.',
                'short_description' => 'Card, PIN, biometric, and face-recognition access control for secure workplaces.',
                'long_description' => 'We design centralized access control, attendance, and visitor-management systems.',
                'seo_description' => 'Access control, biometric reader, attendance, and secure door system installation in Georgia.',
                'seo' => [
                    'title' => 'Access control and attendance systems',
                    'description' => 'Access control, biometric reader, attendance, and secure door system installation in Georgia.',
                ],
                'keywords' => ['access control', 'biometric readers', 'attendance systems'],
                'highlights' => ['Site assessment', 'Secure entry design', 'Central management'],
                'overview' => [
                    'title' => 'Controlled access for every critical area',
                    'paragraphs' => ['We design centralized access control, attendance, and visitor-management systems.'],
                    'stats' => [],
                ],
                'benefits' => [],
                'solutions' => [],
                'industries' => ['Offices', 'Hotels', 'Healthcare', 'Industrial'],
                'process' => [],
                'brands' => [],
                'lead_form' => DefaultCalculatorProfiles::for('access-control'),
                'translations' => $this->serviceTranslations(
                    ['დაშვების კონტროლი', 'Access Control', 'Контроль доступа'],
                    ['დაშვების კონტროლისა და აღრიცხვის სისტემები', 'Access control and attendance systems', 'Системы контроля доступа и учета'],
                    [
                        'ბარათის, PIN-ის, ბიომეტრიისა და სახის ამოცნობის სისტემები დაცული სამუშაო სივრცეებისთვის.',
                        'Card, PIN, biometric, and face-recognition access control for secure workplaces.',
                        'Карточные, PIN-, биометрические системы и распознавание лиц для защищенных объектов.',
                    ],
                ),
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'server-infrastructure',
                'category_slug' => 'server-infrastructure',
                'name' => 'Server Infrastructure',
                'eyebrow' => 'IT infrastructure',
                'icon' => 'server',
                'title' => 'Server infrastructure and virtualization',
                'description' => 'Server deployment, virtualization, storage, backup, and infrastructure monitoring.',
                'short_description' => 'Server deployment, virtualization, storage, backup, and infrastructure monitoring.',
                'long_description' => 'We build resilient server environments with documented recovery and monitoring processes.',
                'seo_description' => 'Server infrastructure, virtualization, storage, backup, and monitoring services in Georgia.',
                'seo' => [
                    'title' => 'Server infrastructure and virtualization',
                    'description' => 'Server infrastructure, virtualization, storage, backup, and monitoring services in Georgia.',
                ],
                'keywords' => ['servers', 'virtualization', 'backup', 'infrastructure monitoring'],
                'highlights' => ['Capacity planning', 'Backup design', 'Infrastructure monitoring'],
                'overview' => [
                    'title' => 'Resilient infrastructure for critical workloads',
                    'paragraphs' => ['We build resilient server environments with documented recovery and monitoring processes.'],
                    'stats' => [],
                ],
                'benefits' => [],
                'solutions' => [],
                'industries' => ['Business', 'Healthcare', 'Hospitality', 'Industrial'],
                'process' => [],
                'brands' => [],
                'lead_form' => DefaultCalculatorProfiles::for('server-infrastructure'),
                'translations' => $this->serviceTranslations(
                    ['სერვერული ინფრასტრუქტურა', 'Server Infrastructure', 'Серверная инфраструктура'],
                    ['სერვერული ინფრასტრუქტურა და ვირტუალიზაცია', 'Server infrastructure and virtualization', 'Серверная инфраструктура и виртуализация'],
                    [
                        'სერვერების დანერგვა, ვირტუალიზაცია, მონაცემთა საცავი, backup და ინფრასტრუქტურის მონიტორინგი.',
                        'Server deployment, virtualization, storage, backup, and infrastructure monitoring.',
                        'Внедрение серверов, виртуализация, хранилища, резервное копирование и мониторинг.',
                    ],
                ),
                'is_published' => true,
                'sort_order' => 4,
            ],
            [
                'slug' => 'it-support',
                'category_slug' => 'it-support',
                'name' => 'IT Support',
                'eyebrow' => 'Managed services',
                'icon' => 'headset',
                'title' => 'Managed IT support for business',
                'description' => 'Remote and on-site IT support, monitoring, asset management, and practical SLA plans.',
                'short_description' => 'Remote and on-site IT support, monitoring, asset management, and practical SLA plans.',
                'long_description' => 'We support users, workstations, servers, and business-critical systems under a clear service plan.',
                'seo_description' => 'Managed IT support, on-site service, remote helpdesk, monitoring, and SLA plans in Georgia.',
                'seo' => [
                    'title' => 'Managed IT support for business',
                    'description' => 'Managed IT support, on-site service, remote helpdesk, monitoring, and SLA plans in Georgia.',
                ],
                'keywords' => ['IT support', 'helpdesk', 'managed IT services', 'SLA'],
                'highlights' => ['Remote helpdesk', 'On-site visits', 'Clear response times'],
                'overview' => [
                    'title' => 'Reliable day-to-day IT operations',
                    'paragraphs' => ['We support users, workstations, servers, and business-critical systems under a clear service plan.'],
                    'stats' => [],
                ],
                'benefits' => [],
                'solutions' => [],
                'industries' => ['Offices', 'Retail', 'Hospitality', 'Professional services'],
                'process' => [],
                'brands' => [],
                'lead_form' => DefaultCalculatorProfiles::for('it-support'),
                'translations' => $this->serviceTranslations(
                    ['IT მხარდაჭერა', 'IT Support', 'IT-поддержка'],
                    ['ბიზნესის მართვადი IT მხარდაჭერა', 'Managed IT support for business', 'Управляемая IT-поддержка бизнеса'],
                    [
                        'დისტანციური და ადგილზე IT მხარდაჭერა, მონიტორინგი, აქტივების მართვა და მკაფიო SLA გეგმები.',
                        'Remote and on-site IT support, monitoring, asset management, and practical SLA plans.',
                        'Удаленная и выездная IT-поддержка, мониторинг, учет активов и понятные SLA.',
                    ],
                ),
                'is_published' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($services as $service) {
            $categorySlug = $service['category_slug'];
            unset($service['category_slug']);
            $service['category_for_service_id'] = $serviceCategories[$categorySlug]->getKey();

            $record = Service::query()->firstOrCreate(
                ['slug' => $service['slug']],
                $service,
            );

            $updates = [];
            if (! is_array($record->translations) || $record->translations === []) {
                $updates['translations'] = $service['translations'];
            }

            if (! is_array($record->lead_form) || ! is_array(data_get($record->lead_form, 'pricing'))) {
                $updates['lead_form'] = $service['lead_form'];
            }

            if (! $record->category_for_service_id) {
                $updates['category_for_service_id'] = $service['category_for_service_id'];
            }

            if ($updates !== []) {
                $record->forceFill($updates)->save();
            }
        }

        $projectCategory = ProjectCategory::query()->firstOrCreate(
            ['slug' => 'offices'],
            [
                'name' => 'ოფისები',
                'sort_order' => 1,
                'seo_title' => 'ოფისების IT ინფრასტრუქტურის პროექტები',
                'seo_description' => 'SafeTech-ის დასრულებული ქსელის, უსაფრთხოებისა და IT ინფრასტრუქტურის პროექტები ოფისებისთვის.',
                'translations' => $this->categoryTranslations(
                    ['ოფისები', 'Offices', 'Офисы'],
                    ['ოფისების IT ინფრასტრუქტურის პროექტები', 'Office IT Infrastructure Projects', 'Проекты IT-инфраструктуры для офисов'],
                    [
                        'SafeTech-ის დასრულებული ქსელის, უსაფრთხოებისა და IT ინფრასტრუქტურის პროექტები ოფისებისთვის.',
                        'Completed SafeTech networking, security, and IT infrastructure projects for offices.',
                        'Завершенные проекты SafeTech по сетям, безопасности и IT-инфраструктуре для офисов.',
                    ],
                ),
            ],
        );

        $projectTranslations = [
            'fields' => [
                'name' => ['ka' => 'ოფისის ქსელის განახლება', 'en' => 'Office Network Upgrade', 'ru' => 'Модернизация офисной сети'],
                'title' => ['ka' => 'ოფისის ქსელის სრული განახლება', 'en' => 'Complete Office Network Upgrade', 'ru' => 'Полная модернизация офисной сети'],
                'description' => [
                    'ka' => 'სტრუქტურული კაბელირების, მართვადი სვიჩებისა და სრულფასოვანი Wi-Fi დაფარვის ერთიანი განახლება.',
                    'en' => 'A complete network refresh with structured cabling, managed switching, and full Wi-Fi coverage.',
                    'ru' => 'Полное обновление сети: структурированная кабельная система, управляемые коммутаторы и полное покрытие Wi-Fi.',
                ],
                'seoTitle' => ['ka' => 'ოფისის ქსელის განახლების პროექტი', 'en' => 'Office Network Upgrade Project', 'ru' => 'Проект модернизации офисной сети'],
                'seoDescription' => [
                    'ka' => 'SafeTech-ის ოფისის ქსელის განახლების პროექტი: სტრუქტურული კაბელირება, მართვადი ქსელი და საიმედო Wi-Fi.',
                    'en' => 'SafeTech office network upgrade project with structured cabling, managed networking, and reliable Wi-Fi.',
                    'ru' => 'Проект SafeTech по модернизации офисной сети: кабельная система, управляемая сеть и надежный Wi-Fi.',
                ],
                'imageAlt' => ['ka' => 'ოფისის ქსელის განახლების პროექტი', 'en' => 'Office network upgrade project', 'ru' => 'Проект модернизации офисной сети'],
                'technology' => ['ka' => 'სტრუქტურული კაბელირება, სვიჩები, Wi-Fi', 'en' => 'Structured cabling, switching, Wi-Fi', 'ru' => 'Структурированная кабельная система, коммутаторы, Wi-Fi'],
            ],
        ];

        $project = Project::query()->firstOrCreate(
            ['slug' => 'office-network-upgrade'],
            [
                'name' => 'Office Network Upgrade',
                'title' => 'Office Network Upgrade',
                'description' => 'A complete network refresh with structured cabling, managed switching, and Wi-Fi coverage.',
                'seo_description' => 'SafeTech office network upgrade case study with structured cabling and managed Wi-Fi.',
                'image_alt' => 'Office network upgrade project',
                'category_id' => $projectCategory->getKey(),
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
                'translations' => $projectTranslations,
            ],
        );

        $projectUpdates = [];

        if (! $project->category_id) {
            $projectUpdates['category_id'] = $projectCategory->getKey();
        }

        if (! is_array($project->translations) || $project->translations === []) {
            $projectUpdates['translations'] = $projectTranslations;
        }

        if ($projectUpdates !== []) {
            $project->forceFill($projectUpdates)->save();
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function serviceCategories(): array
    {
        return [
            $this->serviceCategory(
                'security-systems',
                ['უსაფრთხოების სისტემები', 'Security Systems', 'Системы безопасности'],
                ['უსაფრთხოების სისტემების მონტაჟი', 'Security System Installation', 'Монтаж систем безопасности'],
                [
                    'ვიდეოსამეთვალყურეობისა და დაშვების კონტროლის პროფესიონალური გადაწყვეტილებები ბიზნესისა და საცხოვრებელი ობიექტებისთვის.',
                    'Professional video surveillance and access control solutions for business and residential properties.',
                    'Профессиональные решения видеонаблюдения и контроля доступа для бизнеса и жилых объектов.',
                ],
            ),
            $this->serviceCategory(
                'network-infrastructure',
                ['ქსელური ინფრასტრუქტურა', 'Network Infrastructure', 'Сетевая инфраструктура'],
                ['ქსელის მოწყობა და სტრუქტურული კაბელირება', 'Network Setup and Structured Cabling', 'Монтаж сетей и структурированных кабельных систем'],
                [
                    'სტრუქტურული კაბელირება, Wi-Fi დაფარვა, როუტერები და მართვადი სვიჩები საიმედო ბიზნეს ქსელისთვის.',
                    'Structured cabling, Wi-Fi coverage, routers, and managed switches for reliable business networks.',
                    'Кабельные системы, Wi-Fi, роутеры и управляемые коммутаторы для надежной корпоративной сети.',
                ],
            ),
            $this->serviceCategory(
                'server-infrastructure',
                ['სერვერული ინფრასტრუქტურა', 'Server Infrastructure', 'Серверная инфраструктура'],
                ['სერვერული ინფრასტრუქტურის მოწყობა', 'Server Infrastructure Solutions', 'Решения для серверной инфраструктуры'],
                [
                    'სერვერების, საცავების, ვირტუალიზაციისა და სარეზერვო ასლების დაგეგმვა და გამართვა.',
                    'Planning and deployment of servers, storage, virtualization, and backup systems.',
                    'Проектирование и внедрение серверов, хранилищ, виртуализации и резервного копирования.',
                ],
            ),
            $this->serviceCategory(
                'it-support',
                ['IT მხარდაჭერა', 'IT Support', 'IT-поддержка'],
                ['ბიზნესის IT მხარდაჭერა', 'Business IT Support', 'IT-поддержка бизнеса'],
                [
                    'დისტანციური და ადგილზე ტექნიკური მხარდაჭერა, მონიტორინგი და IT აქტივების მართვა.',
                    'Remote and on-site technical support, monitoring, and IT asset management.',
                    'Удаленная и выездная поддержка, мониторинг и управление IT-активами.',
                ],
            ),
        ];
    }

    /** @param array<int, string> $names @param array<int, string> $titles @param array<int, string> $descriptions */
    private function serviceCategory(string $slug, array $names, array $titles, array $descriptions): array
    {
        return [
            'slug' => $slug,
            'name' => $names[0],
            'seo_title' => $titles[0],
            'seo_description' => $descriptions[0],
            'intro_text' => $descriptions[0],
            'translations' => $this->categoryTranslations($names, $titles, $descriptions),
        ];
    }

    /** @param array<int, string> $names @param array<int, string> $titles @param array<int, string> $descriptions */
    private function categoryTranslations(array $names, array $titles, array $descriptions): array
    {
        return [
            'fields' => [
                'name' => ['ka' => $names[0], 'en' => $names[1], 'ru' => $names[2]],
                'seo_title' => ['ka' => $titles[0], 'en' => $titles[1], 'ru' => $titles[2]],
                'seo_description' => ['ka' => $descriptions[0], 'en' => $descriptions[1], 'ru' => $descriptions[2]],
                'intro_text' => ['ka' => $descriptions[0], 'en' => $descriptions[1], 'ru' => $descriptions[2]],
            ],
        ];
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
                'share_title' => 'გააზიარე ეს გვერდი',
                'share_buttons' => ['facebook', 'whatsapp', 'linkedin', 'link'],
            ],
            'seo' => [
                'site_name' => 'SafeTech',
                'site_description' => '',
                'city' => 'Tbilisi',
                'country' => 'GE',
                'postal_code' => '',
                'lat' => null,
                'lng' => null,
                'open_time' => '09:00',
                'close_time' => '18:00',
            ],
            'branding' => [
                'site_name' => 'SafeTech',
                'tagline' => '',
                'logo' => null,
                'footer_logo' => null,
                'favicon' => null,
                'default_image' => null,
            ],
            'integrations' => [
                'marketing_enabled' => false,
                'google_tag_manager_id' => '',
                'google_analytics_id' => '',
                'meta_pixel_id' => '',
                'google_site_verification' => '',
                'bing_site_verification' => '',
                'yandex_site_verification' => '',
                'indexnow_key' => '',
            ],
            'translations' => [
                'entries' => $this->defaultTranslationEntries(),
            ],
        ];
    }

    /** @param array<int, string> $names
     * @param  array<int, string>  $titles
     * @param  array<int, string>  $descriptions
     * @return array<string, mixed>
     */
    private function serviceTranslations(array $names, array $titles, array $descriptions): array
    {
        return [
            'fields' => [
                'name' => ['ka' => $names[0], 'en' => $names[1], 'ru' => $names[2]],
                'title' => ['ka' => $titles[0], 'en' => $titles[1], 'ru' => $titles[2]],
                'description' => ['ka' => $descriptions[0], 'en' => $descriptions[1], 'ru' => $descriptions[2]],
                'seoDescription' => ['ka' => $descriptions[0], 'en' => $descriptions[1], 'ru' => $descriptions[2]],
            ],
        ];
    }

    private function defaultTranslationEntries(): array
    {
        return array_merge([
            ['key' => 'nav.home', 'ka' => 'მთავარი', 'en' => 'Home', 'ru' => 'Главная'],
            ['key' => 'nav.services', 'ka' => 'სერვისები', 'en' => 'Services', 'ru' => 'Услуги'],
            ['key' => 'nav.calculator', 'ka' => 'კალკულატორი', 'en' => 'Calculator', 'ru' => 'Калькулятор'],
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
            ['key' => 'meta.services.description', 'ka' => 'ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, ქსელები, სერვერები და IT მხარდაჭერა ბიზნესისთვის საქართველოში.', 'en' => 'CCTV, access control, networking, server infrastructure, and managed IT support for businesses in Georgia.', 'ru' => 'Видеонаблюдение, контроль доступа, сети, серверы и IT-поддержка для бизнеса в Грузии.'],
            ['key' => 'meta.serviceCalculator.title', 'ka' => 'IT და უსაფრთხოების სერვისების კალკულატორი', 'en' => 'IT and Security Service Calculator', 'ru' => 'Калькулятор IT-услуг и систем безопасности'],
            ['key' => 'meta.serviceCalculator.description', 'ka' => 'გამოთვალეთ ვიდეოსამეთვალყურეობის, ქსელის, დაშვების კონტროლის, სერვერული ინფრასტრუქტურისა და IT მხარდაჭერის საორიენტაციო ბიუჯეტი.', 'en' => 'Estimate the budget for CCTV, networking, access control, server infrastructure, and managed IT support.', 'ru' => 'Рассчитайте бюджет видеонаблюдения, сети, контроля доступа, серверной инфраструктуры и IT-поддержки.'],
            ['key' => 'meta.projects.title', 'ka' => 'განხორციელებული IT და უსაფრთხოების პროექტები | SafeTech', 'en' => 'Completed IT and Security Projects | SafeTech', 'ru' => 'Реализованные IT- и охранные проекты | SafeTech'],
            ['key' => 'meta.projects.description', 'ka' => 'ნახეთ SafeTech-ის მიერ განხორციელებული ვიდეოსამეთვალყურეობის, ქსელური და სერვერული ინფრასტრუქტურის პროექტები.', 'en' => 'Explore SafeTech CCTV, networking, and server infrastructure projects delivered for businesses.', 'ru' => 'Проекты SafeTech по видеонаблюдению, сетевой и серверной инфраструктуре для бизнеса.'],
            ['key' => 'meta.about.title', 'ka' => 'ჩვენ შესახებ | SafeTech გუნდი და გამოცდილება', 'en' => 'About SafeTech | Team and Experience', 'ru' => 'О SafeTech | Команда и опыт'],
            ['key' => 'meta.about.description', 'ka' => 'გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა უსაფრთხოებისა და IT ინფრასტრუქტურის პროექტებისადმი.', 'en' => 'Meet the SafeTech team and learn how we deliver security and IT infrastructure projects.', 'ru' => 'Познакомьтесь с командой SafeTech и нашим подходом к проектам безопасности и IT-инфраструктуры.'],
            ['key' => 'meta.contact.title', 'ka' => 'კონტაქტი და კონსულტაცია | SafeTech', 'en' => 'Contact and Consultation | SafeTech', 'ru' => 'Контакты и консультация | SafeTech'],
            ['key' => 'meta.contact.description', 'ka' => 'დაუკავშირდით SafeTech-ს IT ინფრასტრუქტურისა და უსაფრთხოების სისტემების კონსულტაციისა და შეთავაზებისთვის.', 'en' => 'Contact SafeTech for an IT infrastructure or security systems consultation and tailored proposal.', 'ru' => 'Свяжитесь с SafeTech для консультации и предложения по IT-инфраструктуре и системам безопасности.'],

            ['key' => 'service.cctv.card.title', 'ka' => 'ვიდეოსამეთვალყურეო სისტემები', 'en' => 'CCTV Systems', 'ru' => 'Системы видеонаблюдения'],
            ['key' => 'service.cctv.card.description', 'ka' => 'პროფესიონალური კამერები ოფისებისთვის, რიტეილისთვის, საწყობებისთვის და საცხოვრებელი სივრცეებისთვის.', 'en' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.', 'ru' => 'Профессиональные камеры для офисов, ритейла, складов и жилых объектов.'],
            ['key' => 'service.networking.card.title', 'ka' => 'ქსელური ინფრასტრუქტურა', 'en' => 'Network Infrastructure', 'ru' => 'Сетевая инфраструктура'],
            ['key' => 'service.networking.card.description', 'ka' => 'სტრუქტურული კაბელირება, როუტერები, სვიჩები, Wi-Fi დაფარვა და დაცული ბიზნეს ქსელები.', 'en' => 'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.', 'ru' => 'Структурированная кабельная система, роутеры, свичи, Wi-Fi покрытие и защищенные бизнес-сети.'],
            ['key' => 'service.access-control.card.title', 'ka' => 'დაშვების კონტროლი', 'en' => 'Access Control', 'ru' => 'Контроль доступа'],
            ['key' => 'service.access-control.card.description', 'ka' => 'ბარათის, PIN-ის, ბიომეტრიისა და სახის ამოცნობის სისტემები დაცული სამუშაო სივრცეებისთვის.', 'en' => 'Card, PIN, biometric, and face-recognition systems for secure workplaces.', 'ru' => 'Карточные, PIN- и биометрические системы для защищенных объектов.'],
            ['key' => 'service.server-infrastructure.card.title', 'ka' => 'სერვერული ინფრასტრუქტურა', 'en' => 'Server Infrastructure', 'ru' => 'Серверная инфраструктура'],
            ['key' => 'service.server-infrastructure.card.description', 'ka' => 'სერვერები, ვირტუალიზაცია, მონაცემთა საცავი, backup და მონიტორინგი.', 'en' => 'Servers, virtualization, storage, backup, and infrastructure monitoring.', 'ru' => 'Серверы, виртуализация, хранилища, резервное копирование и мониторинг.'],
            ['key' => 'service.it-support.card.title', 'ka' => 'IT მხარდაჭერა', 'en' => 'IT Support', 'ru' => 'IT-поддержка'],
            ['key' => 'service.it-support.card.description', 'ka' => 'დისტანციური და ადგილზე მხარდაჭერა, მონიტორინგი, აქტივების მართვა და SLA.', 'en' => 'Remote and on-site support, monitoring, asset management, and SLA plans.', 'ru' => 'Удаленная и выездная поддержка, мониторинг, учет активов и SLA.'],

            ['key' => 'footer.company.calculator', 'ka' => 'კალკულატორი', 'en' => 'Calculator', 'ru' => 'Калькулятор'],

            ['key' => 'common.readMore', 'ka' => 'დეტალურად', 'en' => 'Read more', 'ru' => 'Подробнее'],
            ['key' => 'services.hero.eyebrow', 'ka' => 'უსაფრთხოების სერვისები', 'en' => 'Enterprise Security Solutions', 'ru' => 'Решения для безопасности'],
            ['key' => 'services.hero.titlePrefix', 'ka' => 'პროფესიონალური', 'en' => 'Professional', 'ru' => 'Профессиональные'],
            ['key' => 'services.hero.titleAccent', 'ka' => 'IT და უსაფრთხოების', 'en' => 'IT and security', 'ru' => 'IT- и охранные'],
            ['key' => 'services.hero.titleSuffix', 'ka' => 'სერვისები', 'en' => 'services', 'ru' => 'услуги'],
            ['key' => 'services.hero.description', 'ka' => 'ჩვენ ვუზრუნველყოფთ თქვენი ბიზნესის ციფრულ და ფიზიკურ უსაფრთხოებას უახლესი ტექნოლოგიებითა და ექსპერტული ცოდნით.', 'en' => 'We secure your business with modern digital and physical infrastructure backed by expert implementation.', 'ru' => 'Мы обеспечиваем цифровую и физическую безопасность вашего бизнеса с помощью современных технологий и экспертной реализации.'],
            ['key' => 'services.hero.iso', 'ka' => 'დოკუმენტირებული მიდგომა', 'en' => 'Documented approach', 'ru' => 'Документированный подход'],
            ['key' => 'services.hero.support', 'ka' => 'გრძელვადიანი მხარდაჭერა', 'en' => 'Ongoing support', 'ru' => 'Долгосрочная поддержка'],
            ['key' => 'services.hero.imageAlt', 'ka' => 'კიბერუსაფრთხოებისა და IT ინფრასტრუქტურის სერვისები', 'en' => 'Cybersecurity and IT infrastructure services', 'ru' => 'Услуги кибербезопасности и IT-инфраструктуры'],
            ['key' => 'services.catalog.title', 'ka' => 'სერვისების კატალოგი', 'en' => 'Service Catalog', 'ru' => 'Каталог услуг'],
            ['key' => 'services.catalog.description', 'ka' => 'შეარჩიეთ თქვენს მოთხოვნებზე მორგებული გადაწყვეტა', 'en' => 'Choose the solution that fits your requirements', 'ru' => 'Выберите решение, подходящее под ваши задачи'],
            ['key' => 'services.catalog.page', 'ka' => 'გვერდი 01 / 04', 'en' => 'Page 01 / 04', 'ru' => 'Страница 01 / 04'],
            ['key' => 'services.catalog.count', 'ka' => 'აქტიური სერვისი', 'en' => 'active services', 'ru' => 'активных услуг'],
            ['key' => 'services.catalog.helper', 'ka' => 'ყველა სერვისი ხელმისაწვდომია უშუალოდ ამ გვერდიდან.', 'en' => 'Every service is available directly from this page.', 'ru' => 'Все услуги доступны напрямую с этой страницы.'],
            ['key' => 'services.catalog.empty.title', 'ka' => 'სერვისები ჯერ არ არის დამატებული', 'en' => 'Services have not been added yet', 'ru' => 'Услуги пока не добавлены'],
            ['key' => 'services.catalog.empty.description', 'ka' => 'ადმინისტრატორის მხრიდან კონტენტის დამატების შემდეგ სერვისები აქ ავტომატურად გამოჩნდება.', 'en' => 'Services will appear here automatically after they are added from the admin panel.', 'ru' => 'Услуги автоматически появятся здесь после добавления из админ-панели.'],
            ['key' => 'services.featured.title', 'ka' => 'ყველაზე მოთხოვნადი სერვისები', 'en' => 'Most requested services', 'ru' => 'Самые востребованные услуги'],
            ['key' => 'services.featured.card.imageAlt', 'ka' => 'ვიდეოსამეთვალყურეო სისტემის გამორჩეული გადაწყვეტა', 'en' => 'Featured video surveillance solution', 'ru' => 'Рекомендуемое решение для видеонаблюдения'],
            ['key' => 'services.featured.card.title', 'ka' => 'CCTV გადაწყვეტილებები', 'en' => 'CCTV solutions', 'ru' => 'CCTV-решения'],
            ['key' => 'services.featured.card.description', 'ka' => '24/7 მონიტორინგი და უსაფრთხოება.', 'en' => '24/7 monitoring and security.', 'ru' => 'Круглосуточный мониторинг и безопасность.'],
            ['key' => 'services.work.title', 'ka' => 'მუშაობის პროცესი', 'en' => 'Work process', 'ru' => 'Процесс работы'],
            ['key' => 'services.work.step.0.title', 'ka' => 'კონსულტაცია', 'en' => 'Consultation', 'ru' => 'Консультация'],
            ['key' => 'services.work.step.0.description', 'ka' => 'საჭიროებების განსაზღვრა', 'en' => 'Define requirements', 'ru' => 'Определяем потребности'],
            ['key' => 'services.work.step.1.title', 'ka' => 'ობიექტის შეფასება', 'en' => 'Site assessment', 'ru' => 'Оценка объекта'],
            ['key' => 'services.work.step.1.description', 'ka' => 'ვათვალიერებთ სივრცეს და ტექნიკურ პირობებს.', 'en' => 'We inspect the space and technical conditions.', 'ru' => 'Изучаем пространство и технические условия.'],
            ['key' => 'services.work.step.2.title', 'ka' => 'პროექტირება', 'en' => 'Planning', 'ru' => 'Проектирование'],
            ['key' => 'services.work.step.2.description', 'ka' => 'ვამზადებთ გადაწყვეტას, აღჭურვილობას და ვადებს.', 'en' => 'We prepare the solution, equipment, and timeline.', 'ru' => 'Готовим решение, оборудование и сроки.'],
            ['key' => 'services.work.step.3.title', 'ka' => 'მონტაჟი', 'en' => 'Installation', 'ru' => 'Монтаж'],
            ['key' => 'services.work.step.3.description', 'ka' => 'ვასრულებთ პროფესიონალურ ინსტალაციას.', 'en' => 'We complete the professional installation.', 'ru' => 'Выполняем профессиональную установку.'],
            ['key' => 'services.work.step.4.title', 'ka' => 'გამართვა', 'en' => 'Configuration', 'ru' => 'Настройка'],
            ['key' => 'services.work.step.4.description', 'ka' => 'ვაკონფიგურირებთ სისტემას და ვამოწმებთ მუშაობას.', 'en' => 'We configure the system and test performance.', 'ru' => 'Настраиваем систему и проверяем работу.'],
            ['key' => 'services.work.step.5.title', 'ka' => 'მხარდაჭერა', 'en' => 'Support', 'ru' => 'Поддержка'],
            ['key' => 'services.work.step.5.description', 'ka' => 'გთავაზობთ მომსახურებას გაშვების შემდეგაც.', 'en' => 'We provide support after launch as well.', 'ru' => 'Поддерживаем систему после запуска.'],
            ['key' => 'services.faq.title', 'ka' => 'ხშირად დასმული კითხვები', 'en' => 'Frequently asked questions', 'ru' => 'Часто задаваемые вопросы'],
            ['key' => 'services.faq.description', 'ka' => 'თუ ვერ იპოვეთ თქვენთვის საინტერესო კითხვაზე პასუხი, მოგვწერეთ ან დაგვიკავშირდით.', 'en' => 'If you cannot find the answer you need, write to us or contact our team.', 'ru' => 'Если вы не нашли ответ на свой вопрос, напишите нам или свяжитесь с командой.'],
            ['key' => 'services.faq.contact', 'ka' => 'კონტაქტი', 'en' => 'Contact', 'ru' => 'Контакты'],
            ['key' => 'services.faq.item.0.question', 'ka' => 'რა დრო სჭირდება კამერების მონტაჟს?', 'en' => 'How long does camera installation take?', 'ru' => 'Сколько времени занимает установка камер?'],
            ['key' => 'services.faq.item.0.answer', 'ka' => 'მონტაჟის დრო დამოკიდებულია ობიექტის სირთულესა და კამერების რაოდენობაზე. საშუალოდ, სტანდარტული სისტემის გამართვას 1-2 სამუშაო დღე სჭირდება.', 'en' => 'Installation time depends on the size of the site and the number of cameras. A standard system usually takes 1-2 working days.', 'ru' => 'Срок монтажа зависит от сложности объекта и количества камер. Обычно стандартная система настраивается за 1-2 рабочих дня.'],
            ['key' => 'services.faq.item.1.question', 'ka' => 'გაქვთ თუ არა გარანტია მოწყობილობებზე?', 'en' => 'Do you provide equipment warranty?', 'ru' => 'Предоставляете ли вы гарантию на оборудование?'],
            ['key' => 'services.faq.item.1.answer', 'ka' => 'დიახ, ჩვენს მიერ მოწოდებულ მოწყობილობებსა და შესრულებულ სამუშაოზე ვრცელდება ოფიციალური გარანტია.', 'en' => 'Yes, the equipment we supply and the work we complete are covered by an official warranty.', 'ru' => 'Да, на поставленное нами оборудование и выполненные работы распространяется официальная гарантия.'],
            ['key' => 'services.faq.item.2.question', 'ka' => 'როგორ ხდება Wi-Fi დაფარვის გათვლა?', 'en' => 'How do you calculate Wi-Fi coverage?', 'ru' => 'Как рассчитывается покрытие Wi-Fi?'],
            ['key' => 'services.faq.item.2.answer', 'ka' => 'ვიყენებთ სპეციალურ პროგრამულ უზრუნველყოფას და Site Survey მეთოდოლოგიას, რათა განვსაზღვროთ სიგნალის ოპტიმალური გავრცელება.', 'en' => 'We use specialized software and a Site Survey methodology to define optimal signal coverage.', 'ru' => 'Мы используем специализированное ПО и методологию Site Survey, чтобы определить оптимальное распространение сигнала.'],
            ['key' => 'services.cta.title', 'ka' => 'გჭირდებათ პროფესიონალური IT ინფრასტრუქტურა?', 'en' => 'Need professional IT infrastructure?', 'ru' => 'Нужна профессиональная IT-инфраструктура?'],
            ['key' => 'services.cta.description', 'ka' => 'დაგვიტოვეთ საკონტაქტო ინფორმაცია და ჩვენი ექსპერტი დაგიკავშირდებათ უფასო კონსულტაციისთვის.', 'en' => 'Leave your contact details and our specialist will contact you for a free consultation.', 'ru' => 'Оставьте контактные данные, и наш специалист свяжется с вами для бесплатной консультации.'],
            ['key' => 'services.cta.quote', 'ka' => 'მოითხოვეთ შეთავაზება', 'en' => 'Request an offer', 'ru' => 'Запросить предложение'],
            ['key' => 'services.cta.call', 'ka' => 'დაგვირეკეთ', 'en' => 'Call us', 'ru' => 'Позвоните нам'],
            ['key' => 'service.hero.highlights', 'ka' => 'სერვისის უპირატესობები', 'en' => 'Service highlights', 'ru' => 'Преимущества услуги'],
            ['key' => 'service.hero.consultation', 'ka' => 'უფასო კონსულტაცია', 'en' => 'Free consultation', 'ru' => 'Бесплатная консультация'],
            ['key' => 'service.hero.pricing', 'ka' => 'ფასის მიღება', 'en' => 'Request pricing', 'ru' => 'Запросить цену'],
            ['key' => 'service.share.title', 'ka' => 'გაზიარება', 'en' => 'Share', 'ru' => 'Поделиться'],
            ['key' => 'service.detail.overview.imageAltSuffix', 'ka' => 'ინფრასტრუქტურა', 'en' => 'infrastructure', 'ru' => 'инфраструктура'],
            ['key' => 'service.detail.benefits.eyebrow', 'ka' => 'SafeTech-ის უპირატესობები', 'en' => 'SafeTech advantages', 'ru' => 'Преимущества SafeTech'],
            ['key' => 'service.detail.benefits.title', 'ka' => 'რატომ უნდა აგვირჩიოთ?', 'en' => 'Why choose us?', 'ru' => 'Почему выбирают нас?'],
            ['key' => 'service.detail.solutions.eyebrow', 'ka' => 'მორგებული თქვენს საჭიროებებზე', 'en' => 'Tailored to your needs', 'ru' => 'Под ваши потребности'],
            ['key' => 'service.detail.solutions.title', 'ka' => 'სპეციალიზებული გადაწყვეტილებები', 'en' => 'Specialized solutions', 'ru' => 'Специализированные решения'],
            ['key' => 'service.detail.industries.title', 'ka' => 'ინდუსტრიები', 'en' => 'Industries', 'ru' => 'Отрасли'],
            ['key' => 'service.detail.process.eyebrow', 'ka' => 'გამჭვირვალე პროცესი', 'en' => 'Transparent process', 'ru' => 'Прозрачный процесс'],
            ['key' => 'service.detail.process.title', 'ka' => 'როგორ ვმუშაობთ', 'en' => 'How we work', 'ru' => 'Как мы работаем'],
            ['key' => 'service.detail.partners.ariaLabel', 'ka' => 'ტექნოლოგიური პარტნიორები', 'en' => 'Technology partners', 'ru' => 'Технологические партнеры'],
            ['key' => 'service.detail.faq.eyebrow', 'ka' => 'FAQ', 'en' => 'FAQ', 'ru' => 'FAQ'],
            ['key' => 'service.detail.faq.title', 'ka' => 'ხშირად დასმული კითხვები', 'en' => 'Frequently asked questions', 'ru' => 'Часто задаваемые вопросы'],
            ['key' => 'service.detail.related.title', 'ka' => 'მსგავსი სერვისები', 'en' => 'Related services', 'ru' => 'Похожие услуги'],
            ['key' => 'service.detail.cta.titlePrefix', 'ka' => 'გჭირდებათ', 'en' => 'Need', 'ru' => 'Нужна услуга'],
            ['key' => 'service.detail.cta.description', 'ka' => 'დაგვიკავშირდით და მიიღეთ უფასო პირველადი კონსულტაცია და თქვენს ობიექტზე მორგებული შეთავაზება.', 'en' => 'Contact us and get a free initial consultation with an offer tailored to your site.', 'ru' => 'Свяжитесь с нами и получите бесплатную первичную консультацию и предложение под ваш объект.'],
            ['key' => 'service.detail.cta.consultation', 'ka' => 'უფასო კონსულტაცია', 'en' => 'Free consultation', 'ru' => 'Бесплатная консультация'],
            ['key' => 'service.detail.cta.calculator', 'ka' => 'ფასის გამოთვლა', 'en' => 'Calculate price', 'ru' => 'Рассчитать стоимость'],
            ['key' => 'service.detail.cta.call', 'ka' => 'დაგვირეკეთ', 'en' => 'Call us', 'ru' => 'Позвоните нам'],
        ], $this->extendedTranslationEntries());
    }

    private function extendedTranslationEntries(): array
    {
        $entry = static fn (string $key, string $ka, string $en, string $ru): array => compact(
            'key',
            'ka',
            'en',
            'ru',
        );

        return [
            $entry('accessibility.skipToContent', 'ძირითად კონტენტზე გადასვლა', 'Skip to main content', 'Перейти к основному содержанию'),
            $entry('common.cancel', 'გაუქმება', 'Cancel', 'Отмена'),
            $entry('common.details', 'დეტალურად', 'View details', 'Подробнее'),
            $entry('filters.all', 'ყველა', 'All', 'Все'),
            $entry('nav.mobile.open', 'მენიუს გახსნა', 'Open menu', 'Открыть меню'),
            $entry('nav.region', 'მთავარი ნავიგაცია', 'Main navigation', 'Основная навигация'),
            $entry('pagination.previous', 'წინა', 'Previous', 'Назад'),
            $entry('pagination.next', 'შემდეგი', 'Next', 'Далее'),
            $entry('floating.whatsapp.aria', 'WhatsApp-ით დაკავშირება', 'Contact us on WhatsApp', 'Связаться через WhatsApp'),
            $entry('floating.whatsapp.tooltip', 'მოგვწერეთ WhatsApp-ზე', 'Message us on WhatsApp', 'Напишите нам в WhatsApp'),

            $entry('forms.choose', 'აირჩიეთ', 'Choose', 'Выберите'),
            $entry('forms.company', 'კომპანია', 'Company', 'Компания'),
            $entry('forms.contactHint', 'მიუთითეთ ტელეფონი ან ელფოსტა', 'Enter a phone number or email', 'Укажите телефон или email'),
            $entry('forms.details', 'პროექტის დეტალები', 'Project details', 'Детали проекта'),
            $entry('forms.email', 'ელფოსტა', 'Email', 'Электронная почта'),
            $entry('forms.error.network', 'კავშირის შეცდომა. სცადეთ ხელახლა.', 'Connection error. Please try again.', 'Ошибка соединения. Попробуйте еще раз.'),
            $entry('forms.error.submit', 'მოთხოვნა ვერ გაიგზავნა. გადაამოწმეთ ველები.', 'The request could not be sent. Check the fields.', 'Не удалось отправить запрос. Проверьте поля.'),
            $entry('forms.firstName', 'სახელი', 'First name', 'Имя'),
            $entry('forms.fullName', 'სახელი და გვარი', 'Full name', 'Имя и фамилия'),
            $entry('forms.generalInquiry', 'ზოგადი კონსულტაცია', 'General enquiry', 'Общая консультация'),
            $entry('forms.lastName', 'გვარი', 'Last name', 'Фамилия'),
            $entry('forms.message', 'შეტყობინება', 'Message', 'Сообщение'),
            $entry('forms.phone', 'ტელეფონი', 'Phone', 'Телефон'),
            $entry('forms.phoneNumber', 'ტელეფონის ნომერი', 'Phone number', 'Номер телефона'),
            $entry('forms.privacy', 'ვეთანხმები კონფიდენციალურობის პოლიტიკას', 'I agree to the Privacy Policy', 'Я согласен с Политикой конфиденциальности'),
            $entry('forms.send', 'გაგზავნა', 'Send', 'Отправить'),
            $entry('forms.service', 'სერვისი', 'Service', 'Услуга'),
            $entry('forms.submitRequest', 'მოთხოვნის გაგზავნა', 'Submit request', 'Отправить запрос'),
            $entry('forms.submitting', 'იგზავნება...', 'Sending...', 'Отправка...'),
            $entry('forms.success.submit', 'მოთხოვნა მიღებულია. მალე დაგიკავშირდებით.', 'Your request was received. We will contact you shortly.', 'Запрос получен. Мы скоро свяжемся с вами.'),
            $entry('forms.validation.contact', 'მიუთითეთ ტელეფონი ან ელფოსტა.', 'Enter a phone number or email address.', 'Укажите номер телефона или email.'),
            $entry('contact.form.title', 'მოგვიყევით თქვენი პროექტის შესახებ', 'Tell us about your project', 'Расскажите о вашем проекте'),

            $entry('consent.ariaLabel', 'კონფიდენციალურობის პარამეტრები', 'Privacy settings', 'Настройки конфиденциальности'),
            $entry('consent.message', 'ჩვენ ვიყენებთ აუცილებელ cookies-ს და, თქვენი თანხმობით, ანალიტიკურ და მარკეტინგულ ტექნოლოგიებს.', 'We use essential cookies and, with your consent, analytics and marketing technologies.', 'Мы используем обязательные cookies, а с вашего согласия — аналитические и маркетинговые технологии.'),
            $entry('consent.accept', 'ყველას მიღება', 'Accept all', 'Принять все'),
            $entry('consent.reject', 'მხოლოდ აუცილებელი', 'Essential only', 'Только обязательные'),
            $entry('consent.settings', 'პარამეტრები', 'Settings', 'Настройки'),
            $entry('consent.learnMore', 'დეტალური ინფორმაცია', 'Learn more', 'Подробнее'),
            $entry('consultation.modal.close', 'ფანჯრის დახურვა', 'Close dialog', 'Закрыть окно'),
            $entry('consultation.modal.eyebrow', 'უფასო პირველადი კონსულტაცია', 'Free initial consultation', 'Бесплатная первичная консультация'),
            $entry('consultation.modal.title', 'მოგვიყევით თქვენი საჭიროების შესახებ', 'Tell us what you need', 'Расскажите о вашей задаче'),
            $entry('consultation.modal.description', 'დატოვეთ საკონტაქტო ინფორმაცია და ჩვენი სპეციალისტი დაგიკავშირდებათ.', 'Leave your contact details and our specialist will get in touch.', 'Оставьте контактные данные, и наш специалист свяжется с вами.'),

            $entry('footer.company.title', 'კომპანია', 'Company', 'Компания'),
            $entry('footer.services.title', 'სერვისები', 'Services', 'Услуги'),
            $entry('footer.contact.title', 'კონტაქტი', 'Contact', 'Контакты'),
            $entry('footer.copy.rights', 'ყველა უფლება დაცულია.', 'All rights reserved.', 'Все права защищены.'),
            $entry('notFound.title', 'გვერდი ვერ მოიძებნა', 'Page not found', 'Страница не найдена'),
            $entry('notFound.description', 'მითითებული გვერდი არ არსებობს ან მისამართი შეიცვალა.', 'The requested page does not exist or its address has changed.', 'Запрошенная страница не существует или ее адрес изменился.'),
            $entry('notFound.home', 'მთავარ გვერდზე დაბრუნება', 'Return home', 'Вернуться на главную'),
            $entry('notFound.contact', 'დაგვიკავშირდით', 'Contact us', 'Связаться с нами'),
            $entry('meta.default.title', 'SafeTech | IT ინფრასტრუქტურა და უსაფრთხოების სისტემები', 'SafeTech | IT Infrastructure and Security Systems', 'SafeTech | IT-инфраструктура и системы безопасности'),
            $entry('meta.default.description', 'პროფესიონალური ქსელური, სერვერული და უსაფრთხოების სისტემები ბიზნესისთვის საქართველოში.', 'Professional networking, server, and security systems for businesses in Georgia.', 'Профессиональные сетевые, серверные системы и решения безопасности для бизнеса в Грузии.'),
            $entry('meta.service.notFound', 'სერვისი ვერ მოიძებნა', 'Service not found', 'Услуга не найдена'),
            $entry('meta.project.notFound', 'პროექტი ვერ მოიძებნა', 'Project not found', 'Проект не найден'),

            $entry('home.industries.eyebrow', 'ინდუსტრიები', 'Industries', 'Отрасли'),
            $entry('home.industries.title', 'გადაწყვეტები სხვადასხვა ტიპის ბიზნესისთვის', 'Solutions for different types of business', 'Решения для разных типов бизнеса'),
            $entry('home.industries.description', 'ინფრასტრუქტურას ვარგებთ ობიექტის სამუშაო პროცესს, რისკებსა და განვითარების გეგმას.', 'We adapt infrastructure to the site workflow, risks, and growth plans.', 'Мы адаптируем инфраструктуру к процессам, рискам и планам развития объекта.'),
            $entry('home.industries.items.0', 'ოფისები და ბიზნეს ცენტრები', 'Offices and business centres', 'Офисы и бизнес-центры'),
            $entry('home.industries.items.1', 'საცალო ვაჭრობა და HoReCa', 'Retail and hospitality', 'Ритейл и HoReCa'),
            $entry('home.industries.items.2', 'საწყობები და ლოგისტიკა', 'Warehousing and logistics', 'Склады и логистика'),
            $entry('home.industries.items.3', 'წარმოება და კრიტიკული ობიექტები', 'Manufacturing and critical sites', 'Производство и критические объекты'),
            $entry('home.infrastructure.eyebrow', 'ქსელი და სერვერები', 'Networks and servers', 'Сети и серверы'),
            $entry('home.infrastructure.title', 'სტაბილური საფუძველი ყოველდღიური ოპერაციებისთვის', 'A stable foundation for daily operations', 'Стабильная основа ежедневных операций'),
            $entry('home.infrastructure.description', 'ვგეგმავთ კაბელირებას, ქსელს, Wi-Fi-სა და სერვერულ გარემოს ერთიან, მართვად სისტემად.', 'We design cabling, networking, Wi-Fi, and server environments as one manageable system.', 'Мы проектируем кабельную систему, сеть, Wi-Fi и серверную среду как единую управляемую систему.'),
            $entry('home.infrastructure.imageAlt', 'SafeTech-ის ქსელური და სერვერული ინფრასტრუქტურის გადაწყვეტა', 'SafeTech network and server infrastructure solution', 'Решение SafeTech для сетевой и серверной инфраструктуры'),
            $entry('home.infrastructure.items.0.title', 'წინასწარი პროექტირება', 'Design before deployment', 'Проектирование до внедрения'),
            $entry('home.infrastructure.items.0.description', 'ვადგენთ ტოპოლოგიას, სიმძლავრეს, დაფარვასა და გაფართოების რეზერვს.', 'We define topology, capacity, coverage, and room for expansion.', 'Определяем топологию, емкость, покрытие и резерв для расширения.'),
            $entry('home.infrastructure.items.1.title', 'სტრუქტურული კაბელირება', 'Structured cabling', 'Структурированная кабельная система'),
            $entry('home.infrastructure.items.1.description', 'მარკირებული და დოკუმენტირებული კაბელები ამარტივებს მართვასა და მომსახურებას.', 'Labelled and documented cabling simplifies management and maintenance.', 'Маркированная и документированная кабельная система упрощает управление и обслуживание.'),
            $entry('home.infrastructure.items.2.title', 'მონიტორინგი და მხარდაჭერა', 'Monitoring and support', 'Мониторинг и поддержка'),
            $entry('home.infrastructure.items.2.description', 'ვგეგმავთ ხარვეზების გამოვლენას, backup-სა და შემდგომ მომსახურებას.', 'We plan fault detection, backups, and ongoing maintenance.', 'Планируем обнаружение сбоев, резервное копирование и дальнейшее обслуживание.'),
            $entry('home.trust.title', 'ტექნოლოგიები და პარტნიორები', 'Technology and partners', 'Технологии и партнеры'),
            $entry('home.why.eyebrow', 'რატომ SafeTech', 'Why SafeTech', 'Почему SafeTech'),
            $entry('home.why.title', 'ინფრასტრუქტურა, რომელსაც შეგიძლიათ დაეყრდნოთ', 'Infrastructure you can rely on', 'Инфраструктура, на которую можно положиться'),
            $entry('home.why.description', 'ერთ გუნდში ვაერთიანებთ შეფასებას, პროექტირებას, მონტაჟსა და შემდგომ მხარდაჭერას.', 'One team brings together assessment, design, installation, and ongoing support.', 'Одна команда объединяет оценку, проектирование, монтаж и дальнейшую поддержку.'),
            $entry('home.why.items.0.title', 'საინჟინრო მიდგომა', 'Engineering approach', 'Инженерный подход'),
            $entry('home.why.items.0.description', 'გადაწყვეტა ეფუძნება ობიექტის რეალურ მოთხოვნებს და გაზომვად პარამეტრებს.', 'Solutions are based on real site requirements and measurable parameters.', 'Решения основаны на реальных требованиях объекта и измеримых параметрах.'),
            $entry('home.why.items.1.title', 'უსაფრთხოება პროექტიდანვე', 'Security by design', 'Безопасность на этапе проекта'),
            $entry('home.why.items.1.description', 'წვდომა, სეგმენტაცია და მონაცემთა დაცვა თავიდანვე იგეგმება.', 'Access, segmentation, and data protection are planned from the start.', 'Доступ, сегментация и защита данных планируются с самого начала.'),
            $entry('home.why.items.2.title', 'ერთიანი ეკოსისტემა', 'Integrated systems', 'Единая экосистема'),
            $entry('home.why.items.2.description', 'ქსელი, სერვერები და ფიზიკური უსაფრთხოება შეთანხმებულად მუშაობს.', 'Networks, servers, and physical security work together.', 'Сети, серверы и физическая безопасность работают согласованно.'),
            $entry('home.why.items.3.title', 'პასუხისმგებელი მხარდაჭერა', 'Responsible support', 'Ответственная поддержка'),
            $entry('home.why.items.3.description', 'სამუშაოს დასრულების შემდეგაც ვეხმარებით სისტემის გამართულ მართვაში.', 'We continue helping you manage the system after delivery.', 'Мы продолжаем помогать управлять системой после сдачи проекта.'),
            $entry('home.why.items.4.title', 'დოკუმენტირებული სისტემა', 'Documented system', 'Документированная система'),
            $entry('home.why.items.4.description', 'სქემები და კონფიგურაცია ამცირებს მომავალ გაურკვევლობას.', 'Diagrams and configuration records reduce future uncertainty.', 'Схемы и данные конфигурации уменьшают неопределенность в будущем.'),
            $entry('home.why.items.5.title', 'განვითარებისთვის მზად', 'Ready to scale', 'Готовность к развитию'),
            $entry('home.why.items.5.description', 'ინფრასტრუქტურაში ვტოვებთ ზრდისა და ახალი მოწყობილობების ინტეგრაციის შესაძლებლობას.', 'Infrastructure is planned for growth and integration of new equipment.', 'Инфраструктура предусматривает рост и интеграцию нового оборудования.'),
            $entry('home.cta.eyebrow', 'დაიწყეთ კონსულტაციით', 'Start with a consultation', 'Начните с консультации'),
            $entry('home.cta.title', 'გაქვთ ახალი პროექტი ან არსებული სისტემის პრობლემა?', 'Planning a project or facing an infrastructure issue?', 'Планируете проект или решаете проблему инфраструктуры?'),
            $entry('home.cta.description', 'დატოვეთ ელფოსტა და მოკლედ განვიხილავთ შემდეგ პრაქტიკულ ნაბიჯს.', 'Leave your email and we will discuss the next practical step.', 'Оставьте email, и мы обсудим следующий практический шаг.'),
            $entry('home.cta.emailLabel', 'ელფოსტა', 'Email address', 'Электронная почта'),
            $entry('home.cta.emailPlaceholder', 'name@company.ge', 'name@company.com', 'name@company.com'),
            $entry('home.cta.submit', 'კონსულტაციის მოთხოვნა', 'Request consultation', 'Запросить консультацию'),
            $entry('home.cta.note', 'თქვენს მონაცემებს მხოლოდ მოთხოვნაზე პასუხისთვის გამოვიყენებთ.', 'We use your details only to respond to the request.', 'Мы используем ваши данные только для ответа на запрос.'),

            $entry('about.hero.title', 'SafeTech-ის შესახებ', 'About SafeTech', 'О SafeTech'),
            $entry('about.hero.description', 'ვქმნით და ვმართავთ ბიზნესისთვის საჭირო IT ინფრასტრუქტურასა და უსაფრთხოების სისტემებს.', 'We design and manage the IT infrastructure and security systems businesses need.', 'Мы проектируем и обслуживаем IT-инфраструктуру и системы безопасности для бизнеса.'),
            $entry('about.hero.cta.primary', 'კონსულტაციის მოთხოვნა', 'Request consultation', 'Запросить консультацию'),
            $entry('about.hero.cta.secondary', 'ჩვენი სერვისები', 'Our services', 'Наши услуги'),
            $entry('about.story.title', 'ტექნოლოგია კონკრეტული ბიზნეს ამოცანისთვის', 'Technology built around a business need', 'Технологии под конкретную бизнес-задачу'),
            $entry('about.story.paragraph.0', 'ჩვენი მუშაობა იწყება ობიექტის, რისკებისა და ოპერაციული მოთხოვნების გაგებით. ამის შემდეგ ვარჩევთ არქიტექტურასა და მოწყობილობებს.', 'Our work starts by understanding the site, risks, and operational requirements. We then choose the architecture and equipment.', 'Работа начинается с понимания объекта, рисков и операционных требований. Затем мы выбираем архитектуру и оборудование.'),
            $entry('about.story.paragraph.1', 'მიზანია გამართული, გასაგები და განვითარებისთვის მზად სისტემა, რომლის მართვაც პროექტის დასრულების შემდეგაც მარტივია.', 'The goal is a reliable, understandable system that is ready to grow and remains manageable after delivery.', 'Цель — надежная, понятная и готовая к развитию система, которой легко управлять после сдачи проекта.'),
            $entry('about.story.imageAlt', 'SafeTech-ის გუნდი ინფრასტრუქტურის პროექტზე მუშაობს', 'SafeTech team working on an infrastructure project', 'Команда SafeTech работает над инфраструктурным проектом'),
            $entry('about.who.title', 'რას ვაკეთებთ', 'What we do', 'Что мы делаем'),
            $entry('about.who.description', 'ვაერთიანებთ ფიზიკურ უსაფრთხოებას, ქსელსა და IT ოპერაციებს ერთ პასუხისმგებელ პროცესში.', 'We combine physical security, networking, and IT operations in one accountable process.', 'Мы объединяем физическую безопасность, сети и IT-операции в одном ответственном процессе.'),
            $entry('about.who.item.0.title', 'უსაფრთხოების სისტემები', 'Security systems', 'Системы безопасности'),
            $entry('about.who.item.0.description', 'ვიდეოსამეთვალყურეობა, დაშვების კონტროლი და აღრიცხვა.', 'CCTV, access control, and attendance systems.', 'Видеонаблюдение, контроль доступа и учет.'),
            $entry('about.who.item.1.title', 'ქსელური ინფრასტრუქტურა', 'Network infrastructure', 'Сетевая инфраструктура'),
            $entry('about.who.item.1.description', 'კაბელირება, Wi-Fi, როუტინგი, switching და სეგმენტაცია.', 'Cabling, Wi-Fi, routing, switching, and segmentation.', 'Кабельная система, Wi-Fi, маршрутизация, коммутация и сегментация.'),
            $entry('about.who.item.2.title', 'სერვერები და მხარდაჭერა', 'Servers and support', 'Серверы и поддержка'),
            $entry('about.who.item.2.description', 'ვირტუალიზაცია, backup, მონიტორინგი და მომხმარებლების მხარდაჭერა.', 'Virtualisation, backups, monitoring, and user support.', 'Виртуализация, резервное копирование, мониторинг и поддержка пользователей.'),
            $entry('about.what.item.0.index', '01', '01', '01'),
            $entry('about.what.item.0.title', 'ვაფასებთ', 'Assess', 'Оцениваем'),
            $entry('about.what.item.0.description', 'ვაგროვებთ მოთხოვნებს და ვამოწმებთ არსებულ გარემოს.', 'We gather requirements and inspect the existing environment.', 'Собираем требования и проверяем существующую среду.'),
            $entry('about.what.item.1.index', '02', '02', '02'),
            $entry('about.what.item.1.title', 'ვაპროექტებთ', 'Design', 'Проектируем'),
            $entry('about.what.item.1.description', 'ვამზადებთ არქიტექტურას, სპეციფიკაციასა და სამუშაო გეგმას.', 'We prepare the architecture, specification, and work plan.', 'Готовим архитектуру, спецификацию и план работ.'),
            $entry('about.what.item.2.index', '03', '03', '03'),
            $entry('about.what.item.2.title', 'ვნერგავთ და ვუჭერთ მხარს', 'Deploy and support', 'Внедряем и поддерживаем'),
            $entry('about.what.item.2.description', 'ვაწყობთ სისტემას, ვტესტავთ და ვგეგმავთ შემდგომ მომსახურებას.', 'We build, test, and plan ongoing maintenance.', 'Внедряем, тестируем и планируем дальнейшее обслуживание.'),
            $entry('about.how.title', 'როგორ მიდის პროექტი', 'How a project progresses', 'Как проходит проект'),
            $entry('about.how.item.0.title', 'საჭიროებების გარკვევა', 'Requirements discovery', 'Определение требований'),
            $entry('about.how.item.0.description', 'ვაზუსტებთ მიზნებს, სივრცეს, მომხმარებლებსა და შეზღუდვებს.', 'We clarify goals, spaces, users, and constraints.', 'Уточняем цели, помещения, пользователей и ограничения.'),
            $entry('about.how.item.1.title', 'ტექნიკური შეფასება', 'Technical assessment', 'Техническая оценка'),
            $entry('about.how.item.1.description', 'ვამოწმებთ ინფრასტრუქტურას და ვადგენთ საჭირო გაზომვებს.', 'We inspect the infrastructure and complete the required measurements.', 'Проверяем инфраструктуру и выполняем необходимые измерения.'),
            $entry('about.how.item.2.title', 'დანერგვა და ტესტირება', 'Deployment and testing', 'Внедрение и тестирование'),
            $entry('about.how.item.2.description', 'ვახორციელებთ მონტაჟს, კონფიგურაციასა და მიღების ტესტებს.', 'We complete installation, configuration, and acceptance testing.', 'Выполняем монтаж, настройку и приемочные испытания.'),
            $entry('about.how.item.3.title', 'გადაცემა და მხარდაჭერა', 'Handover and support', 'Передача и поддержка'),
            $entry('about.how.item.3.description', 'ვაწვდით დოკუმენტაციას და ვათანხმებთ შემდგომ მომსახურებას.', 'We provide documentation and agree the ongoing support plan.', 'Передаем документацию и согласовываем дальнейшую поддержку.'),
            $entry('about.numbers.item.0.value', '5', '5', '5'),
            $entry('about.numbers.item.0.label', 'ძირითადი სერვისის მიმართულება', 'core service areas', 'основных направлений услуг'),
            $entry('about.numbers.item.1.value', '3', '3', '3'),
            $entry('about.numbers.item.1.label', 'კონტენტის ენა', 'content languages', 'языка контента'),
            $entry('about.numbers.item.2.value', '1', '1', '1'),
            $entry('about.numbers.item.2.label', 'პასუხისმგებელი გუნდი პროექტზე', 'accountable project team', 'ответственная команда проекта'),
            $entry('about.numbers.item.3.value', '360°', '360°', '360°'),
            $entry('about.numbers.item.3.label', 'ინფრასტრუქტურის ერთიანი ხედვა', 'integrated infrastructure view', 'комплексный взгляд на инфраструктуру'),
            $entry('about.team.eyebrow', 'ჩვენი გუნდი', 'Our team', 'Наша команда'),
            $entry('about.team.title', 'სპეციალისტები, რომლებიც შედეგზე პასუხისმგებლობას იღებენ', 'Specialists accountable for the result', 'Специалисты, отвечающие за результат'),
            $entry('about.team.description', 'ადმინისტრაციიდან დამატებული გუნდის წევრები აქ ავტომატურად გამოჩნდება.', 'Team members added in the admin panel appear here automatically.', 'Сотрудники, добавленные в админ-панели, автоматически появятся здесь.'),
            $entry('about.team.regionLabel', 'SafeTech-ის გუნდის წევრები', 'SafeTech team members', 'Сотрудники SafeTech'),
            $entry('about.why.title', 'რატომ გვანდობენ ინფრასტრუქტურას', 'Why clients trust us with infrastructure', 'Почему нам доверяют инфраструктуру'),
            $entry('about.why.description', 'ფოკუსი გვაქვს გამართულ მუშაობაზე, გამჭვირვალე პროცესსა და გრძელვადიან მართვადობაზე.', 'We focus on reliable operation, a transparent process, and long-term manageability.', 'Мы ориентируемся на надежную работу, прозрачный процесс и долгосрочную управляемость.'),
            $entry('about.why.item.0.title', 'გეგმაზე დაფუძნებული სამუშაო', 'Plan-led delivery', 'Работа по плану'),
            $entry('about.why.item.0.description', 'სამუშაოს დაწყებამდე ვათანხმებთ მოთხოვნებს, მოცულობასა და ეტაპებს.', 'Requirements, scope, and stages are agreed before work starts.', 'До начала работ согласовываем требования, объем и этапы.'),
            $entry('about.why.item.1.title', 'ტესტირებადი შედეგი', 'Testable outcomes', 'Проверяемый результат'),
            $entry('about.why.item.1.description', 'სისტემას ვამოწმებთ შეთანხმებული ფუნქციური კრიტერიუმებით.', 'Systems are tested against agreed functional criteria.', 'Проверяем системы по согласованным функциональным критериям.'),
            $entry('about.why.item.2.title', 'ერთიანი პასუხისმგებლობა', 'Single point of accountability', 'Единая ответственность'),
            $entry('about.why.item.2.description', 'ინტეგრირებული პროექტის კოორდინაცია ერთ გუნდში რჩება.', 'Integrated project coordination remains with one team.', 'Координация интегрированного проекта остается у одной команды.'),
            $entry('about.why.item.3.title', 'შემდგომი მხარდაჭერა', 'Ongoing support', 'Дальнейшая поддержка'),
            $entry('about.why.item.3.description', 'გადაცემის შემდეგ შესაძლებელია მომსახურებისა და განვითარების გაგრძელება.', 'Maintenance and future development can continue after handover.', 'После передачи можно продолжить обслуживание и развитие.'),
            $entry('about.cta.title', 'განვიხილოთ თქვენი ინფრასტრუქტურის ამოცანა', 'Let us discuss your infrastructure needs', 'Обсудим задачи вашей инфраструктуры'),
            $entry('about.cta.description', 'მოკლე კონსულტაცია დაგვეხმარება სწორი ტექნიკური შემდეგი ნაბიჯის განსაზღვრაში.', 'A short consultation helps identify the right technical next step.', 'Короткая консультация поможет определить правильный следующий технический шаг.'),
            $entry('about.cta.button', 'კონსულტაციის მოთხოვნა', 'Request consultation', 'Запросить консультацию'),

            $entry('projects.hero.eyebrow', 'განხორციელებული სამუშაოები', 'Delivered work', 'Реализованные работы'),
            $entry('projects.hero.title', 'IT და უსაფრთხოების პროექტები', 'IT and security projects', 'Проекты IT и безопасности'),
            $entry('projects.hero.description', 'ნახეთ როგორ ვგეგმავთ და ვნერგავთ ქსელურ, სერვერულ და უსაფრთხოების ინფრასტრუქტურას.', 'See how we design and deploy network, server, and security infrastructure.', 'Посмотрите, как мы проектируем и внедряем сетевую, серверную инфраструктуру и системы безопасности.'),
            $entry('projects.hero.imageAlt', 'SafeTech-ის განხორციელებული ინფრასტრუქტურის პროექტი', 'Infrastructure project delivered by SafeTech', 'Инфраструктурный проект, реализованный SafeTech'),
            $entry('projects.metrics.projects', 'პროექტი', 'projects', 'проектов'),
            $entry('projects.metrics.featured', 'გამორჩეული', 'featured', 'избранных'),
            $entry('projects.metrics.categories', 'კატეგორია', 'categories', 'категорий'),
            $entry('projects.metrics.technologies', 'ტექნოლოგია', 'technologies', 'технологий'),
            $entry('projects.metrics.region', 'პროექტების მაჩვენებლები', 'Project metrics', 'Показатели проектов'),
            $entry('projects.featured.title', 'გამორჩეული პროექტები', 'Featured projects', 'Избранные проекты'),
            $entry('projects.featured.description', 'შერჩეული სამუშაოები, სადაც ტექნიკური ამოცანა სრულ გადაწყვეტად გარდაიქმნა.', 'Selected work where a technical requirement became a complete solution.', 'Избранные работы, где техническая задача стала комплексным решением.'),
            $entry('projects.gallery.title', 'ყველა პროექტი', 'All projects', 'Все проекты'),
            $entry('projects.gallery.description', 'გაფილტრეთ პროექტები მიმართულების მიხედვით.', 'Filter projects by area.', 'Фильтруйте проекты по направлению.'),
            $entry('projects.completed', 'დასრულებული პროექტი', 'Completed project', 'Завершенный проект'),
            $entry('projects.video.open', 'პროექტის ვიდეოს გახსნა', 'Open project video', 'Открыть видео проекта'),
            $entry('projects.cta.title', 'გაქვთ მსგავსი ტექნიკური ამოცანა?', 'Have a similar technical requirement?', 'Есть похожая техническая задача?'),
            $entry('projects.cta.description', 'მოგვაწოდეთ საწყისი ინფორმაცია და მოვამზადებთ შეფასების სწორ გზას.', 'Share the initial details and we will recommend the right assessment path.', 'Сообщите исходные данные, и мы предложим подходящий формат оценки.'),
            $entry('projects.cta.button', 'პროექტის განხილვა', 'Discuss a project', 'Обсудить проект'),

            $entry('project.detail.hero.badge', 'განხორციელებული პროექტი', 'Delivered project', 'Реализованный проект'),
            $entry('project.detail.hero.primaryCta', 'მსგავსი პროექტის განხილვა', 'Discuss a similar project', 'Обсудить похожий проект'),
            $entry('project.detail.hero.secondaryCta', 'ყველა პროექტი', 'All projects', 'Все проекты'),
            $entry('project.detail.overview.scopeTitle', 'სამუშაოს მოცულობა', 'Project scope', 'Объем работ'),
            $entry('project.detail.overview.specsTitle', 'ტექნიკური პარამეტრები', 'Technical specifications', 'Технические параметры'),
            $entry('project.detail.challenges.title', 'საწყისი გამოწვევები', 'Initial challenges', 'Исходные задачи'),
            $entry('project.detail.solutions.title', 'დანერგილი გადაწყვეტილებები', 'Implemented solutions', 'Внедренные решения'),
            $entry('project.detail.process.title', 'განხორციელების პროცესი', 'Delivery process', 'Процесс реализации'),
            $entry('project.detail.process.stepLabel', 'ეტაპი', 'Step', 'Этап'),
            $entry('project.detail.results.title', 'მიღებული შედეგები', 'Project outcomes', 'Результаты проекта'),
            $entry('project.detail.related.title', 'მსგავსი პროექტები', 'Related projects', 'Похожие проекты'),
            $entry('project.detail.cta.title', 'დაგეგმეთ თქვენი პროექტი SafeTech-თან', 'Plan your project with SafeTech', 'Спланируйте проект с SafeTech'),
            $entry('project.detail.cta.description', 'მივიღებთ საწყის მოთხოვნებს, შევაფასებთ ობიექტს და შემოგთავაზებთ განხორციელების გეგმას.', 'We gather requirements, assess the site, and propose a delivery plan.', 'Мы соберем требования, оценим объект и предложим план реализации.'),
            $entry('project.detail.cta.primary', 'კონსულტაციის მოთხოვნა', 'Request consultation', 'Запросить консультацию'),
            $entry('project.detail.cta.secondary', 'დაგვირეკეთ', 'Call us', 'Позвонить'),

            $entry('blog.title', 'პრაქტიკული გზამკვლევები და სიახლეები', 'Practical guides and updates', 'Практические материалы и новости'),
            $entry('blog.filter.all', 'ყველა სტატია', 'All articles', 'Все статьи'),
            $entry('blog.empty', 'ამ კატეგორიაში სტატია ჯერ არ არის გამოქვეყნებული.', 'No articles have been published in this category yet.', 'В этой категории пока нет опубликованных статей.'),
            $entry('blog.breadcrumb', 'ბლოგი', 'Blog', 'Блог'),
            $entry('blog.section', 'სტატია', 'Article', 'Статья'),
            $entry('blog.contents', 'სარჩევი', 'Contents', 'Содержание'),
            $entry('blog.minRead', 'წთ. საკითხავი', 'min read', 'мин. чтения'),
            $entry('blog.related', 'მსგავსი სტატიები', 'Related articles', 'Похожие статьи'),

            $entry('contact.hero.title', 'დაგვიკავშირდით', 'Contact SafeTech', 'Свяжитесь с SafeTech'),
            $entry('contact.hero.description', 'მოგვიყევით თქვენი ობიექტის, სისტემის ან IT მხარდაჭერის საჭიროების შესახებ.', 'Tell us about your site, system, or IT support needs.', 'Расскажите о вашем объекте, системе или потребностях в IT-поддержке.'),
            $entry('contact.hero.button', 'მოთხოვნის გაგზავნა', 'Send an enquiry', 'Отправить запрос'),
            $entry('contact.intro.title', 'დავიწყოთ სწორი კითხვებით', 'Start with the right questions', 'Начнем с правильных вопросов'),
            $entry('contact.intro.paragraph.0', 'მიუთითეთ რომელი სერვისი გაინტერესებთ, ობიექტის ტიპი და პროექტის სავარაუდო მასშტაბი.', 'Tell us which service you need, the type of site, and the approximate scope.', 'Укажите нужную услугу, тип объекта и примерный масштаб проекта.'),
            $entry('contact.intro.paragraph.1', 'თუ ზუსტი სპეციფიკაცია ჯერ არ გაქვთ, დაგეხმარებით მოთხოვნების ჩამოყალიბებაში.', 'If you do not yet have a specification, we will help define the requirements.', 'Если спецификации пока нет, мы поможем сформулировать требования.'),
            $entry('contact.intro.badge.0', 'პირველადი კონსულტაცია', 'Initial consultation', 'Первичная консультация'),
            $entry('contact.intro.badge.1', 'ობიექტზე მორგებული შეფასება', 'Site-specific assessment', 'Оценка под конкретный объект'),
            $entry('contact.intro.imageAlt', 'SafeTech-ის სპეციალისტთან ტექნიკური კონსულტაცია', 'Technical consultation with a SafeTech specialist', 'Техническая консультация со специалистом SafeTech'),
            $entry('contact.side.title', 'სანამ ფორმას შეავსებთ', 'Before you submit the form', 'Перед отправкой формы'),
            $entry('contact.side.description', 'სასარგებლოა ობიექტის მისამართი, ფართობი, მოწყობილობების რაოდენობა, ვადები და არსებული ინფრასტრუქტურის მოკლე აღწერა.', 'Useful details include the site location, area, equipment quantities, timeline, and a brief description of existing infrastructure.', 'Полезно указать адрес объекта, площадь, количество оборудования, сроки и краткое описание существующей инфраструктуры.'),
            $entry('contact.info.phone', 'ტელეფონი', 'Phone', 'Телефон'),
            $entry('contact.info.email', 'ელფოსტა', 'Email', 'Электронная почта'),
            $entry('contact.info.address', 'მისამართი', 'Address', 'Адрес'),
            $entry('contact.info.hours', 'სამუშაო საათები', 'Business hours', 'Часы работы'),
            $entry('contact.support.badge', 'ტექნიკური მხარდაჭერა', 'Technical support', 'Техническая поддержка'),
            $entry('contact.support.title', 'არსებული სისტემის პრობლემა გაქვთ?', 'Need help with an existing system?', 'Нужна помощь с существующей системой?'),
            $entry('contact.support.description', 'აღწერეთ სიმპტომი, მოწყობილობა და რამდენ მომხმარებელზე ან სივრცეზე მოქმედებს პრობლემა.', 'Describe the symptom, equipment, and how many users or areas are affected.', 'Опишите симптом, оборудование и сколько пользователей или зон затронуто.'),
            $entry('contact.support.imageAlt', 'SafeTech-ის ტექნიკური მხარდაჭერის სპეციალისტი', 'SafeTech technical support specialist', 'Специалист технической поддержки SafeTech'),
            $entry('contact.faq.title', 'ხშირად დასმული კითხვები', 'Frequently asked questions', 'Часто задаваемые вопросы'),
            $entry('contact.final.title', 'მზად ხართ შემდეგი ნაბიჯისთვის?', 'Ready for the next step?', 'Готовы к следующему шагу?'),
            $entry('contact.final.button', 'დაგვიკავშირდით', 'Contact us', 'Связаться с нами'),

            $entry('calculator.empty', 'კალკულატორის პროფილები ჯერ არ არის გამოქვეყნებული.', 'Calculator profiles have not been published yet.', 'Профили калькулятора пока не опубликованы.'),
            $entry('calculator.hero.eyebrow', 'სერვისების კალკულატორი', 'Service calculator', 'Калькулятор услуг'),
            $entry('calculator.hero.title', 'მიიღეთ პროექტის საორიენტაციო ბიუჯეტი', 'Estimate your project budget', 'Рассчитайте ориентировочный бюджет проекта'),
            $entry('calculator.hero.description', 'აირჩიეთ სერვისი და მიუთითეთ ობიექტის პარამეტრები. საბოლოო შეთავაზება ტექნიკური შეფასების შემდეგ მომზადდება.', 'Choose a service and enter the site details. The final offer is prepared after a technical assessment.', 'Выберите услугу и параметры объекта. Итоговое предложение готовится после технической оценки.'),
            $entry('calculator.form.service', 'სერვისი', 'Service', 'Услуга'),
            $entry('calculator.form.package', 'მომსახურების პაკეტი', 'Service package', 'Пакет обслуживания'),
            $entry('calculator.form.recommended', 'რეკომენდებული', 'Recommended', 'Рекомендуемый'),
            $entry('calculator.summary.oneTime', 'ერთჯერადი საორიენტაციო ღირებულება', 'Estimated one-time cost', 'Ориентировочная разовая стоимость'),
            $entry('calculator.summary.monthly', 'ყოველთვიური საორიენტაციო ღირებულება', 'Estimated monthly cost', 'Ориентировочная ежемесячная стоимость'),
            $entry('calculator.summary.print', 'შედეგის დაბეჭდვა', 'Print estimate', 'Распечатать расчет'),
            $entry('calculator.contact.title', 'მიიღეთ დაზუსტებული შეთავაზება', 'Request a detailed proposal', 'Запросить уточненное предложение'),
            $entry('calculator.contact.send', 'შეთავაზების მოთხოვნა', 'Request proposal', 'Запросить предложение'),
        ];
    }

    private function seedPrivacyPolicy(): void
    {
        if (PrivacyPolicy::query()->exists()) {
            return;
        }

        $content = [
            'ka' => <<<'HTML'
<h2>რა ინფორმაციას ვაგროვებთ</h2>
<p>როდესაც გვიკავშირდებით ან ავსებთ ფორმას, შეიძლება მივიღოთ თქვენი სახელი, ტელეფონი, ელფოსტა, კომპანიის დასახელება და მოთხოვნის ტექნიკური დეტალები. საიტის უსაფრთხოებისა და მუშაობის გასაუმჯობესებლად შეიძლება დამუშავდეს ტექნიკური ჟურნალები და, მხოლოდ შესაბამისი თანხმობის შემთხვევაში, ანალიტიკური მონაცემები.</p>
<h2>როგორ ვიყენებთ ინფორმაციას</h2>
<p>ინფორმაციას ვიყენებთ თქვენს მოთხოვნაზე პასუხის გასაცემად, კონსულტაციისა და შეთავაზების მოსამზადებლად, მომსახურების შესასრულებლად, სისტემების უსაფრთხოების დასაცავად და საიტის ხარისხის გასაუმჯობესებლად.</p>
<h2>Cookies და ანალიტიკა</h2>
<p>აუცილებელი cookies გამოიყენება საიტის ძირითადი ფუნქციებისთვის. ანალიტიკური და მარკეტინგული ტექნოლოგიები აქტიურდება მხოლოდ თქვენი არჩევანის შესაბამისად, რომლის შეცვლაც ნებისმიერ დროს შეგიძლიათ.</p>
<h2>გაზიარება და შენახვა</h2>
<p>პერსონალურ მონაცემებს არ ვყიდით. მონაცემები შეიძლება გადაეცეს მხოლოდ იმ სანდო ტექნიკურ მომწოდებლებს, რომლებიც გვეხმარებიან საიტის, ელფოსტის, ჰოსტინგის ან მომხმარებელთა მოთხოვნების მართვაში. ინფორმაციას ვინახავთ მხოლოდ იმდენ ხანს, რამდენიც საჭიროა შესაბამისი მიზნისა და სამართლებრივი ვალდებულებისთვის.</p>
<h2>თქვენი უფლებები</h2>
<p>შეგიძლიათ მოითხოვოთ თქვენს მონაცემებზე წვდომა, გასწორება, წაშლა ან დამუშავების შეზღუდვა, მოქმედი კანონმდებლობის ფარგლებში. მოთხოვნისთვის გამოიყენეთ საიტის საკონტაქტო გვერდი.</p>
HTML,
            'en' => <<<'HTML'
<h2>Information we collect</h2>
<p>When you contact us or submit a form, we may receive your name, phone number, email address, company name, and technical details of your request. Technical logs may be processed to protect and improve the website, while analytics data is processed only when the relevant consent has been given.</p>
<h2>How we use information</h2>
<p>We use information to respond to enquiries, prepare consultations and proposals, deliver requested services, protect our systems, and improve the quality of the website.</p>
<h2>Cookies and analytics</h2>
<p>Essential cookies support the website's core functions. Analytics and marketing technologies are enabled according to your choice, which you can change at any time.</p>
<h2>Sharing and retention</h2>
<p>We do not sell personal data. Data may be shared only with trusted technical providers that support our website, email, hosting, or enquiry management. We retain information only for as long as required for the relevant purpose and legal obligations.</p>
<h2>Your rights</h2>
<p>You may request access, correction, deletion, or restriction of your personal data where applicable under law. Use the website's contact page to submit a request.</p>
HTML,
            'ru' => <<<'HTML'
<h2>Какие данные мы собираем</h2>
<p>Когда вы связываетесь с нами или отправляете форму, мы можем получить ваше имя, номер телефона, адрес электронной почты, название компании и технические детали запроса. Для защиты и улучшения сайта могут обрабатываться технические журналы, а аналитические данные обрабатываются только при наличии соответствующего согласия.</p>
<h2>Как мы используем данные</h2>
<p>Мы используем информацию, чтобы отвечать на запросы, готовить консультации и предложения, оказывать заказанные услуги, защищать системы и улучшать качество сайта.</p>
<h2>Cookies и аналитика</h2>
<p>Обязательные cookies обеспечивают основные функции сайта. Аналитические и маркетинговые технологии включаются в соответствии с вашим выбором, который можно изменить в любое время.</p>
<h2>Передача и хранение</h2>
<p>Мы не продаем персональные данные. Данные могут передаваться только надежным техническим поставщикам, обеспечивающим работу сайта, электронной почты, хостинга или обработку обращений. Информация хранится только в течение срока, необходимого для соответствующей цели и выполнения правовых обязательств.</p>
<h2>Ваши права</h2>
<p>В предусмотренных законом случаях вы можете запросить доступ, исправление, удаление или ограничение обработки персональных данных. Для запроса используйте контактную страницу сайта.</p>
HTML,
        ];

        PrivacyPolicy::query()->create([
            'title' => 'კონფიდენციალურობის პოლიტიკა',
            'highlight' => 'როგორ ვაგროვებთ, ვიყენებთ და ვიცავთ თქვენს ინფორმაციას.',
            'content' => $content['ka'],
            'translations' => [
                'fields' => [
                    'title' => [
                        'ka' => 'კონფიდენციალურობის პოლიტიკა',
                        'en' => 'Privacy Policy',
                        'ru' => 'Политика конфиденциальности',
                    ],
                    'highlight' => [
                        'ka' => 'როგორ ვაგროვებთ, ვიყენებთ და ვიცავთ თქვენს ინფორმაციას.',
                        'en' => 'How we collect, use, and protect your information.',
                        'ru' => 'Как мы собираем, используем и защищаем вашу информацию.',
                    ],
                    'content' => $content,
                ],
            ],
        ]);
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
