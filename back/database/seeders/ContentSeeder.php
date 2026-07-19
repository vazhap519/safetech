<?php

namespace Database\Seeders;

use App\Models\Project;
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

            if ($updates !== []) {
                $record->forceFill($updates)->save();
            }
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
     *  @param array<int, string> $titles
     *  @param array<int, string> $descriptions
     *  @return array<string, mixed>
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
        return [
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
            ['key' => 'services.hero.iso', 'ka' => 'ISO 27001 სერტიფიცირებული', 'en' => 'ISO 27001 Certified', 'ru' => 'Сертификация ISO 27001'],
            ['key' => 'services.hero.support', 'ka' => '24/7 მხარდაჭერა', 'en' => '24/7 Support', 'ru' => 'Поддержка 24/7'],
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
