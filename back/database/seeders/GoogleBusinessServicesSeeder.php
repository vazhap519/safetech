<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CategoryForService;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\CanonicalSeedTombstones;
use App\Support\MultilingualContent;
use Illuminate\Database\Seeder;

/**
 * Mirrors the 47 services entered into SafeTech's Google Business Profile.
 * Run AFTER ServiceCatalogSeeder; preserve CMS copy, existing slugs, media and FAQs.
 * Newly created short-detail pages are published but noindexed until enriched.
 */
final class GoogleBusinessServicesSeeder extends Seeder
{
    /** @return list<string> */
    public static function canonicalCategorySlugs(): array
    {
        return array_keys((new self)->categories());
    }

    /** @return list<string> */
    public static function canonicalServiceSlugs(): array
    {
        return array_column((new self)->services(), 'slug');
    }

    public function run(): void
    {
        $categories = [];

        foreach ($this->categories() as $slug => $definition) {
            if (CanonicalSeedTombstones::categoryWasDeleted($slug)) {
                continue;
            }

            $record = CategoryForService::query()->firstOrNew(['slug' => $slug]);
            if (! $record->exists) {
                $record->name = $definition['name']['ka'];
                $record->intro_text = $definition['description']['ka'];
                $record->seo_title = $record->name.' | SafeTech';
                $record->seo_description = $record->intro_text;
                $record->noindex = false;
            }
            $categoryTranslations = $record->translations ?? [];
            if ($record->exists && $definition['legacy_name'] !== ''
                && $record->name === $definition['legacy_name']) {
                // Rename unchanged canonical defaults, not editor-written names.
                $record->name = $definition['name']['ka'];
                data_set($categoryTranslations, 'fields.name', $definition['name']);
            }
            $record->translations = $this->mergeMissingTranslations(
                $categoryTranslations,
                ['name' => $definition['name'], 'intro_text' => $definition['description']],
            );
            $record->save();
            $categories[$slug] = $record;
        }

        $sort = 0;
        foreach ($this->services() as $definition) {
            ++$sort;
            $slug = $definition['slug'];
            $category = $categories[$definition['category']] ?? null;
            if ($category === null || CanonicalSeedTombstones::serviceWasDeleted($slug)) {
                continue;
            }

            $service = Service::query()->firstOrNew(['slug' => $slug]);
            if (! $service->exists) {
                $service->name = $definition['name']['ka'];
                $service->title = $definition['name']['ka'];
                $service->short_description = $definition['description']['ka'];
                $service->description = $definition['description']['ka'];
                $service->long_description = $definition['description']['ka'];
                $service->seo_description = $definition['description']['ka'];
                $service->seo = [
                    'title' => $definition['name']['ka'].' | SafeTech',
                    'description' => $definition['description']['ka'],
                    'noindex' => true, // New short pages need fuller content before indexing.
                ];
                $service->is_published = true;
                $service->sort_order = $sort;
            } else {
                // Never clobber an administrator's full SEO page or gallery.
                // For re-used canonical URLs, keep their existing title and copy.
                // The category assignment is intentionally aligned to Google.
                if (blank($service->name)) {
                    $service->name = $definition['name']['ka'];
                }
            }
            $serviceTranslations = $service->translations ?? [];
            if ($service->exists && $definition['legacy_name'] !== ''
                && $service->name === $definition['legacy_name']) {
                $service->name = $definition['name']['ka'];
                $serviceTranslations = $this->updateUneditedCanonicalNames($serviceTranslations, $definition);
            }
            $service->category_for_service_id = $category->getKey();
            $service->translations = $this->mergeMissingTranslations(
                $serviceTranslations,
                [
                    'name' => $definition['name'],
                    'title' => $definition['name'],
                    'description' => $definition['description'],
                    'card' => [
                        'title' => $definition['name'],
                        'description' => $definition['description'],
                    ],
                ],
            );
            $service->save();
        }

        $this->syncPublicServiceNames();
    }

    /**
     * Keep existing editor translations while upgrading known shipped labels
     * from the former canonical service catalog.
     */
    private function updateUneditedCanonicalNames(array $translations, array $definition): array
    {
        $legacy = $this->legacyLocalizedNames()[$definition['slug']] ?? [];

        foreach (['name', 'card.title'] as $field) {
            foreach (['ka', 'en', 'ru'] as $locale) {
                $current = trim((string) data_get($translations, "fields.{$field}.{$locale}", ''));
                if ($current === '' || ($legacy[$locale] ?? null) === $current) {
                    data_set($translations, "fields.{$field}.{$locale}", $definition['name'][$locale]);
                }
            }
        }

        return $translations;
    }

    /**
     * Frontend public translation entries can take precedence over model
     * translations; rename only original generated labels, not CMS overrides.
     */
    private function syncPublicServiceNames(): void
    {
        if (CanonicalSeedTombstones::siteSettingWasDeleted('translations')) {
            return;
        }

        $setting = SiteSetting::query()->where('key', 'translations')->first();
        if ($setting === null || ! is_array($setting->value)) {
            return;
        }

        $value = $setting->value;
        $map = MultilingualContent::mapFrom($value);
        $legacyNames = $this->legacyLocalizedNames();
        $changed = false;

        foreach ($this->services() as $definition) {
            $legacy = $legacyNames[$definition['slug']] ?? null;
            if ($legacy === null
                || ! Service::query()->where('slug', $definition['slug'])
                    ->where('name', $definition['name']['ka'])->exists()) {
                continue;
            }

            foreach (['name', 'card.title'] as $field) {
                $key = "service.{$definition['slug']}.{$field}";
                if (CanonicalSeedTombstones::translationEntryWasDeleted($key)) {
                    continue;
                }

                foreach (['ka', 'en', 'ru'] as $locale) {
                    $current = trim((string) ($map[$key][$locale] ?? ''));
                    if ($current === '' || $current === $legacy[$locale]) {
                        $map[$key][$locale] = $definition['name'][$locale];
                        $changed = true;
                    }
                }
            }
        }

        if ($changed) {
            $value['entries'] = MultilingualContent::entriesFromMap($map);
            $setting->value = $value;
            $setting->save();
        }
    }

    /** @return array<string, array{ka:string,en:string,ru:string}> */
    private function legacyLocalizedNames(): array
    {
        return [
            'operating-system-installation' => ['ka' => 'ოპერაციული სისტემების ინსტალაცია', 'en' => 'Operating System Installation', 'ru' => 'Установка операционных систем'],
            'custom-computer-build' => ['ka' => 'კომპიუტერების აწყობა', 'en' => 'Custom Computer Assembly', 'ru' => 'Сборка компьютеров'],
            'computer-cleaning-maintenance' => ['ka' => 'კომპიუტერების გაწმენდა და პროფილაქტიკა', 'en' => 'Computer Cleaning and Preventive Maintenance', 'ru' => 'Чистка и профилактика компьютеров'],
            'rack-assembly-cable-management' => ['ka' => 'რეკების აწყობა და კაბელ მენეჯმენტი', 'en' => 'Rack Assembly and Cable Management', 'ru' => 'Сборка шкафов и кабель-менеджмент'],
            'business-it-support' => ['ka' => 'IT მხარდაჭერა', 'en' => 'IT Support', 'ru' => 'IT-поддержка'],
            'security-camera-installation' => ['ka' => 'უსაფრთხოების კამერების მონტაჟი და გამართვა', 'en' => 'Security Camera Installation and Setup', 'ru' => 'Монтаж и настройка камер видеонаблюдения'],
            'intercom-access-control-installation' => ['ka' => 'დომოფონებისა და დაშვების სისტემების მონტაჟი', 'en' => 'Intercom and Access Control Installation', 'ru' => 'Монтаж домофонов и систем контроля доступа'],
            'router-wifi-configuration' => ['ka' => 'როუტერების ინსტალაცია და კონფიგურაცია', 'en' => 'Router Installation and Configuration', 'ru' => 'Установка и настройка роутеров'],
            'network-cable-installation' => ['ka' => 'ქსელის კაბელის გაყვანა', 'en' => 'Network Cable Installation', 'ru' => 'Прокладка сетевого кабеля'],
            'patch-panel-network-outlet-installation' => ['ka' => 'Patch Panel-ის და ქსელური როზეტების მონტაჟი', 'en' => 'Patch Panel and Network Outlet Installation', 'ru' => 'Монтаж патч-панелей и сетевых розеток'],
            'barrier-gate-installation' => ['ka' => 'შლაგბაუმების მონტაჟი', 'en' => 'Barrier Gate Installation', 'ru' => 'Монтаж шлагбаумов'],
        ];
    }

    private function mergeMissingTranslations(?array $translations, array $fields): array
    {
        $translations ??= [];
        foreach ($fields as $key => $value) {
            if ($key === 'card') {
                foreach ($value as $cardKey => $localized) {
                    foreach (['ka', 'en', 'ru'] as $locale) {
                        if (blank(data_get($translations, "fields.card.{$cardKey}.{$locale}"))) {
                            data_set($translations, "fields.card.{$cardKey}.{$locale}", $localized[$locale]);
                        }
                    }
                }
                continue;
            }
            foreach (['ka', 'en', 'ru'] as $locale) {
                if (blank(data_get($translations, "fields.{$key}.{$locale}"))) {
                    data_set($translations, "fields.{$key}.{$locale}", $value[$locale]);
                }
            }
        }
        return $translations;
    }

    /** @return array<string,array<string,array<string,string>>> */
    private function categories(): array
    {
        return [
            'security-access-automation' => ['legacy_name' => 'უსაფრთხოება და დაშვების ავტომატიკა', 'name' => ['ka' => 'უსაფრთხოების სისტემების მონტაჟი', 'en' => 'Security System Installation', 'ru' => 'Монтаж систем безопасности'], 'description' => ['ka' => 'კამერების, დომოფონების, სიგნალიზაციისა და დაშვების კონტროლის მონტაჟი.', 'en' => 'Installation of CCTV, intercom, alarms and access control.', 'ru' => 'Монтаж видеонаблюдения, домофонов, сигнализации и контроля доступа.']],
            'computer-services' => ['legacy_name' => 'კომპიუტერული სერვისები', 'name' => ['ka' => 'კომპიუტერული მომსახურება', 'en' => 'Computer Services', 'ru' => 'Компьютерные услуги'], 'description' => ['ka' => 'კომპიუტერის აწყობა, განახლება, გამართვა და პროფილაქტიკური მომსახურება.', 'en' => 'PC assembly, upgrades, configuration and preventive maintenance.', 'ru' => 'Сборка, модернизация, настройка и профилактика компьютеров.']],
            'network-infrastructure' => ['legacy_name' => 'ქსელური ინფრასტრუქტურა', 'name' => ['ka' => 'ქსელური ინფრასტრუქტურა', 'en' => 'Computer Networking Services', 'ru' => 'Компьютерные сети'], 'description' => ['ka' => 'CAT6, RJ45, LAN, Wi-Fi, ქსელური კარადები და Patch Panel.', 'en' => 'CAT6, RJ45, LAN, Wi-Fi, network racks and patch panels.', 'ru' => 'CAT6, RJ45, LAN, Wi-Fi, сетевые шкафы и патч-панели.']],
            'business-it' => ['legacy_name' => 'ბიზნეს IT სისტემები', 'name' => ['ka' => 'IT მხარდაჭერა და მომსახურება', 'en' => 'IT Support and Services', 'ru' => 'IT-поддержка и услуги'], 'description' => ['ka' => 'Windows, macOS, პროგრამები, ელფოსტა და ბიზნესის IT მხარდაჭერა.', 'en' => 'Windows, macOS, software, business email and IT support.', 'ru' => 'Windows, macOS, программы, корпоративная почта и IT-поддержка.']],
            'telecommunications-contractor' => ['legacy_name' => '', 'name' => ['ka' => 'სუსტი დენები და საკომუნიკაციო ინფრასტრუქტურა', 'en' => 'Low-Voltage and Communications Infrastructure', 'ru' => 'Слаботочные системы и связь'], 'description' => ['ka' => 'საცხოვრებელი და კომერციული ობიექტების სუსტი დენების გაყვანა და სტრუქტურირებული დაკაბელება.', 'en' => 'Low-voltage systems and structured cabling for residential and commercial buildings.', 'ru' => 'Слаботочные системы и структурированная кабельная сеть для жилых и коммерческих объектов.']],
        ];
    }

    /** @return array<int,array<string,mixed>> */
    private function services(): array
    {
        return [
            ['category' => 'security-access-automation', 'slug' => 'ip-camera-installation', 'legacy_name' => '', 'name' => ['ka' => 'IP კამერების მონტაჟი', 'en' => 'IP Camera Installation', 'ru' => 'Монтаж IP-камер'], 'description' => ['ka' => 'IP კამერების მონტაჟი, PoE ქსელთან მიერთება, NVR-ისა და დისტანციური ხედვის გამართვა.', 'en' => 'Installation of IP cameras, PoE connection, NVR setup and remote viewing.', 'ru' => 'Монтаж IP-камер, подключение PoE, настройка NVR и удаленного просмотра.']],
            ['category' => 'security-access-automation', 'slug' => 'security-camera-installation', 'legacy_name' => 'უსაფრთხოების კამერების მონტაჟი და გამართვა', 'name' => ['ka' => 'ვიდეომეთვალყურეობის სისტემების მონტაჟი', 'en' => 'CCTV System Installation', 'ru' => 'Монтаж видеонаблюдения'], 'description' => ['ka' => 'IP და ანალოგური კამერების, NVR/DVR-ის, ჩაწერისა და დისტანციური მონიტორინგის გამართვა.', 'en' => 'IP and analog CCTV, NVR/DVR recording and remote monitoring setup.', 'ru' => 'Настройка IP и аналогового видеонаблюдения, NVR/DVR и удаленного доступа.']],
            ['category' => 'security-access-automation', 'slug' => 'intercom-access-control-installation', 'legacy_name' => 'დომოფონებისა და დაშვების სისტემების მონტაჟი', 'name' => ['ka' => 'დომოფონების მონტაჟი', 'en' => 'Intercom Installation', 'ru' => 'Монтаж домофонов'], 'description' => ['ka' => 'აუდიო/ვიდეოდომოფონების, მონიტორების, საკეტებისა და გასვლის ღილაკების მონტაჟი.', 'en' => 'Audio/video intercoms, monitors, door locks and exit buttons.', 'ru' => 'Монтаж аудио- и видеодомофонов, мониторов, замков и кнопок выхода.']],
            ['category' => 'security-access-automation', 'slug' => 'access-control-installation', 'legacy_name' => '', 'name' => ['ka' => 'დაშვების კონტროლის სისტემების მონტაჟი', 'en' => 'Access Control Installation', 'ru' => 'Монтаж систем контроля доступа'], 'description' => ['ka' => 'RFID, PIN, კონტროლერების, საკეტებისა და გასვლის ღილაკების მიერთება და გამართვა.', 'en' => 'RFID/PIN access, controllers, locks and exit buttons installation and configuration.', 'ru' => 'Установка и настройка RFID/PIN доступа, контроллеров, замков и кнопок выхода.']],
            ['category' => 'security-access-automation', 'slug' => 'alarm-system-installation', 'legacy_name' => '', 'name' => ['ka' => 'სიგნალიზაციის მონტაჟი', 'en' => 'Alarm System Installation', 'ru' => 'Монтаж сигнализации'], 'description' => ['ka' => 'სიგნალიზაციის, დეტექტორების, სირენებისა და მობილური შეტყობინებების გამართვა.', 'en' => 'Alarm panels, detectors, sirens and mobile alerts configuration.', 'ru' => 'Монтаж сигнализации, датчиков, сирен и настройка уведомлений.']],
            ['category' => 'security-access-automation', 'slug' => 'barrier-gate-installation', 'legacy_name' => 'შლაგბაუმების მონტაჟი', 'name' => ['ka' => 'შლაგბაუმების მონტაჟი და გამართვა', 'en' => 'Barrier Gate Installation and Setup', 'ru' => 'Монтаж и настройка шлагбаумов'], 'description' => ['ka' => 'ავტომატური შლაგბაუმების, პულტების, უსაფრთხოების სენსორებისა და თავსებადი LPR მართვის გამართვა.', 'en' => 'Automatic barriers, remote controls, safety sensors and compatible LPR access.', 'ru' => 'Монтаж шлагбаумов, пультов, датчиков безопасности и совместимого LPR.']],
            ['category' => 'computer-services', 'slug' => 'onsite-computer-service', 'legacy_name' => '', 'name' => ['ka' => 'კომპიუტერის ადგილზე მომსახურება', 'en' => 'On-Site Computer Service', 'ru' => 'Выездное обслуживание компьютеров'], 'description' => ['ka' => 'სახლსა და ოფისში კომპიუტერის დიაგნოსტიკა, პროგრამული გამართვა და ტექნიკური დახმარება.', 'en' => 'On-site computer diagnostics, software configuration and assistance at home or office.', 'ru' => 'Выездная диагностика и настройка компьютера дома и в офисе.']],
            ['category' => 'computer-services', 'slug' => 'custom-computer-build', 'legacy_name' => 'კომპიუტერების აწყობა', 'name' => ['ka' => 'პერსონალური კომპიუტერის აწყობა', 'en' => 'Custom PC Assembly', 'ru' => 'Сборка персонального компьютера'], 'description' => ['ka' => 'თავსებადი კომპონენტების შერჩევა, კომპიუტერის აწყობა, BIOS-ის გამართვა და ტესტირება.', 'en' => 'Component selection, custom PC assembly, BIOS setup and testing.', 'ru' => 'Подбор комплектующих, сборка ПК, настройка BIOS и тестирование.']],
            ['category' => 'computer-services', 'slug' => 'computer-setup-optimization', 'legacy_name' => '', 'name' => ['ka' => 'კომპიუტერის გამართვა და ოპტიმიზაცია', 'en' => 'Computer Setup and Optimization', 'ru' => 'Настройка и оптимизация компьютера'], 'description' => ['ka' => 'Windows-ის, დრაივერებისა და გაშვების პარამეტრების მოწესრიგება და წარმადობის შემოწმება.', 'en' => 'Windows, drivers, startup settings and performance optimization.', 'ru' => 'Настройка Windows, драйверов, автозагрузки и производительности.']],
            ['category' => 'computer-services', 'slug' => 'computer-component-upgrades', 'legacy_name' => '', 'name' => ['ka' => 'კომპიუტერის კომპონენტების განახლება', 'en' => 'PC Component Upgrades', 'ru' => 'Модернизация комплектующих ПК'], 'description' => ['ka' => 'SSD-ის, RAM-ისა და სხვა თავსებადი კომპონენტების შერჩევა, მონტაჟი და ტესტირება.', 'en' => 'Selection, installation and testing of compatible SSD, RAM and other PC parts.', 'ru' => 'Подбор, установка и тестирование SSD, RAM и совместимых компонентов.']],
            ['category' => 'computer-services', 'slug' => 'ram-upgrade', 'legacy_name' => '', 'name' => ['ka' => 'ოპერატიული მეხსიერების (RAM) განახლება', 'en' => 'RAM Upgrade', 'ru' => 'Увеличение оперативной памяти RAM'], 'description' => ['ka' => 'RAM-ის თავსებადობის შემოწმება, შერჩევა, მონტაჟი და სისტემის ტესტირება.', 'en' => 'RAM compatibility check, selection, installation and system test.', 'ru' => 'Проверка совместимости, подбор, установка и проверка RAM.']],
            ['category' => 'computer-services', 'slug' => 'computer-cleaning-maintenance', 'legacy_name' => 'კომპიუტერების გაწმენდა და პროფილაქტიკა', 'name' => ['ka' => 'კომპიუტერის პროფილაქტიკური მომსახურება', 'en' => 'Computer Preventive Maintenance', 'ru' => 'Профилактика компьютера'], 'description' => ['ka' => 'მტვრისგან გაწმენდა, გაგრილების შემოწმება და საჭიროების შემთხვევაში თერმოპასტის შეცვლა.', 'en' => 'Dust cleaning, cooling inspection and thermal paste replacement when needed.', 'ru' => 'Чистка пыли, проверка охлаждения и замена термопасты при необходимости.']],
            ['category' => 'computer-services', 'slug' => 'new-computer-setup', 'legacy_name' => '', 'name' => ['ka' => 'ახალი კომპიუტერის მომზადება სამუშაოდ', 'en' => 'New Computer Setup', 'ru' => 'Подготовка нового компьютера к работе'], 'description' => ['ka' => 'ახალი კომპიუტერის Windows-ის, პროგრამების, ელფოსტისა და ქსელის გამართვა.', 'en' => 'Set up Windows, software, email and networking on a new PC.', 'ru' => 'Настройка Windows, программ, почты и сети на новом компьютере.']],
            ['category' => 'computer-services', 'slug' => 'computer-peripheral-setup', 'legacy_name' => '', 'name' => ['ka' => 'კომპიუტერისა და პერიფერიული მოწყობილობების მიერთება', 'en' => 'Computer and Peripheral Setup', 'ru' => 'Подключение ПК и периферии'], 'description' => ['ka' => 'მონიტორების, პრინტერების, სკანერებისა და სხვა პერიფერიის დაკავშირება და ტესტირება.', 'en' => 'Connection and testing of monitors, printers, scanners and other peripherals.', 'ru' => 'Подключение и проверка мониторов, принтеров, сканеров и периферии.']],
            ['category' => 'network-infrastructure', 'slug' => 'low-voltage-cabling', 'legacy_name' => '', 'name' => ['ka' => 'სუსტი დენების გაყვანა', 'en' => 'Low-Voltage Cabling', 'ru' => 'Прокладка слаботочных кабелей'], 'description' => ['ka' => 'ქსელის, კამერების, დომოფონებისა და დაშვების სისტემების საკაბელო ხაზების გაყვანა.', 'en' => 'Cabling for networking, CCTV, intercom and access control systems.', 'ru' => 'Прокладка кабелей сети, камер, домофонов и контроля доступа.']],
            ['category' => 'network-infrastructure', 'slug' => 'residential-network-cabling', 'legacy_name' => '', 'name' => ['ka' => 'კერძო სახლების ქსელური დაკაბელება', 'en' => 'Residential Network Cabling', 'ru' => 'Сетевая разводка частных домов'], 'description' => ['ka' => 'კერძო სახლებში CAT6 კაბელების, RJ45 როზეტებისა და Wi-Fi წერტილების მოწყობა.', 'en' => 'CAT6, RJ45 outlets and Wi-Fi access points for houses and cottages.', 'ru' => 'Монтаж CAT6, RJ45 и точек Wi-Fi в частных домах и коттеджах.']],
            ['category' => 'network-infrastructure', 'slug' => 'network-cable-installation', 'legacy_name' => 'ქსელის კაბელის გაყვანა', 'name' => ['ka' => 'CAT6 ქსელის კაბელის გაყვანა', 'en' => 'CAT6 Network Cabling', 'ru' => 'Прокладка сетевого кабеля CAT6'], 'description' => ['ka' => 'CAT6 ქსელური კაბელების გაყვანა, მარკირება, ტერმინაცია და შემოწმება.', 'en' => 'CAT6 cable routing, labeling, termination and testing.', 'ru' => 'Прокладка, маркировка, обжим и тестирование кабеля CAT6.']],
            ['category' => 'network-infrastructure', 'slug' => 'rj45-outlet-installation', 'legacy_name' => '', 'name' => ['ka' => 'RJ45 ქსელური როზეტების მონტაჟი', 'en' => 'RJ45 Network Outlet Installation', 'ru' => 'Монтаж сетевых розеток RJ45'], 'description' => ['ka' => 'RJ45 როზეტების დაერთება, მარკირება და ქსელური წერტილების ტესტირება.', 'en' => 'RJ45 outlet termination, labeling and network-point testing.', 'ru' => 'Подключение розеток RJ45, маркировка и проверка линий.']],
            ['category' => 'network-infrastructure', 'slug' => 'lan-network-installation', 'legacy_name' => '', 'name' => ['ka' => 'LAN ქსელის მონტაჟი', 'en' => 'LAN Network Installation', 'ru' => 'Монтаж локальной сети LAN'], 'description' => ['ka' => 'ლოკალური ქსელის კაბელების, როუტერებისა და კომუტატორების მიერთება და გამართვა.', 'en' => 'LAN cabling, routers, switches, configuration and connectivity testing.', 'ru' => 'Монтаж LAN, настройка роутеров, коммутаторов и проверка связи.']],
            ['category' => 'network-infrastructure', 'slug' => 'router-wifi-configuration', 'legacy_name' => 'როუტერების ინსტალაცია და კონფიგურაცია', 'name' => ['ka' => 'Wi-Fi ქსელის მონტაჟი და გამართვა', 'en' => 'Wi-Fi Installation and Setup', 'ru' => 'Монтаж и настройка Wi-Fi'], 'description' => ['ka' => 'როუტერებისა და Access Point-ების გამართვა, დაფარვის გაუმჯობესება და Mesh ქსელი.', 'en' => 'Router and access point setup, coverage improvement and Mesh Wi-Fi.', 'ru' => 'Настройка роутеров, точек доступа, покрытия и Mesh Wi-Fi.']],
            ['category' => 'network-infrastructure', 'slug' => 'rack-assembly-cable-management', 'legacy_name' => 'რეკების აწყობა და კაბელ მენეჯმენტი', 'name' => ['ka' => 'ქსელური კარადების მონტაჟი', 'en' => 'Network Rack Installation', 'ru' => 'Монтаж сетевых шкафов'], 'description' => ['ka' => 'Rack კარადების, Switch-ის, NVR-ის, UPS-ისა და კაბელების მოწესრიგებული განთავსება.', 'en' => 'Rack, switch, NVR, UPS installation and organized cabling.', 'ru' => 'Монтаж сетевых шкафов, Switch, NVR, UPS и организация кабелей.']],
            ['category' => 'network-infrastructure', 'slug' => 'patch-panel-network-outlet-installation', 'legacy_name' => 'Patch Panel-ის და ქსელური როზეტების მონტაჟი', 'name' => ['ka' => 'Patch Panel-ის მონტაჟი და დაერთება', 'en' => 'Patch Panel Installation', 'ru' => 'Монтаж и подключение патч-панели'], 'description' => ['ka' => 'Patch Panel-ის მონტაჟი, CAT6-ის ტერმინაცია, პორტების მარკირება და ტესტირება.', 'en' => 'Patch-panel installation, CAT6 termination, port labeling and testing.', 'ru' => 'Монтаж патч-панели, подключение CAT6, маркировка портов и тестирование.']],
            ['category' => 'business-it', 'slug' => 'business-it-support', 'legacy_name' => 'IT მხარდაჭერა', 'name' => ['ka' => 'IT ტექნიკური მხარდაჭერა', 'en' => 'IT Technical Support', 'ru' => 'Техническая IT-поддержка'], 'description' => ['ka' => 'კომპიუტერების, პროგრამების, პრინტერებისა და ქსელების ადგილზე ან დისტანციური მხარდაჭერა.', 'en' => 'On-site or remote support for computers, software, printers and networks.', 'ru' => 'Выездная и удаленная помощь с компьютерами, ПО, принтерами и сетями.']],
            ['category' => 'business-it', 'slug' => 'office-it-infrastructure', 'legacy_name' => '', 'name' => ['ka' => 'ოფისების IT ინფრასტრუქტურის მოწყობა', 'en' => 'Office IT Infrastructure Setup', 'ru' => 'IT-инфраструктура офиса'], 'description' => ['ka' => 'ოფისის სამუშაო კომპიუტერების, LAN/Wi-Fi ქსელისა და პრინტერების გამართვა.', 'en' => 'Office workstations, LAN/Wi-Fi networks and printers setup.', 'ru' => 'Настройка рабочих мест, LAN/Wi-Fi и принтеров в офисе.']],
            ['category' => 'business-it', 'slug' => 'workstation-setup', 'legacy_name' => '', 'name' => ['ka' => 'კომპიუტერებისა და სამუშაო ადგილების გამართვა', 'en' => 'Computer and Workstation Setup', 'ru' => 'Настройка компьютеров и рабочих мест'], 'description' => ['ka' => 'სამუშაო კომპიუტერების, პროგრამების, ანგარიშებისა და პერიფერიის გამართვა.', 'en' => 'Configuration of workstations, software, user accounts and peripherals.', 'ru' => 'Настройка рабочих ПК, программ, учетных записей и периферии.']],
            ['category' => 'business-it', 'slug' => 'operating-system-installation', 'legacy_name' => 'ოპერაციული სისტემების ინსტალაცია', 'name' => ['ka' => 'Windows-ის ინსტალაცია და კონფიგურაცია', 'en' => 'Windows Installation and Configuration', 'ru' => 'Установка и настройка Windows'], 'description' => ['ka' => 'Windows-ის ინსტალაცია, დრაივერები, განახლებები და საჭირო პროგრამების გამართვა.', 'en' => 'Windows installation, drivers, updates and essential software configuration.', 'ru' => 'Установка Windows, драйверов, обновлений и необходимых программ.']],
            ['category' => 'business-it', 'slug' => 'software-installation', 'legacy_name' => '', 'name' => ['ka' => 'პროგრამული უზრუნველყოფის ინსტალაცია', 'en' => 'Software Installation', 'ru' => 'Установка программного обеспечения'], 'description' => ['ka' => 'საოფისე და პროფესიული პროგრამების ინსტალაცია, განახლება და გამართვა.', 'en' => 'Installation, updates and configuration of office and specialist software.', 'ru' => 'Установка, обновление и настройка офисного и профильного ПО.']],
            ['category' => 'business-it', 'slug' => 'virus-removal-optimization', 'legacy_name' => '', 'name' => ['ka' => 'ვირუსების მოცილება და სისტემის ოპტიმიზაცია', 'en' => 'Virus Removal and Optimization', 'ru' => 'Удаление вирусов и оптимизация'], 'description' => ['ka' => 'Windows-ზე ვირუსებისა და არასასურველი პროგრამების მოცილება, თუ გადაყენება საჭირო არ არის.', 'en' => 'Remove malware and unwanted software from Windows when reinstalling is unnecessary.', 'ru' => 'Удаление вредоносных программ Windows без переустановки, если возможно.']],
            ['category' => 'business-it', 'slug' => 'network-printer-scanner-setup', 'legacy_name' => '', 'name' => ['ka' => 'ქსელური პრინტერებისა და სკანერების გამართვა', 'en' => 'Network Printer and Scanner Setup', 'ru' => 'Настройка сетевых принтеров и сканеров'], 'description' => ['ka' => 'პრინტერებისა და სკანერების LAN/Wi-Fi ქსელთან მიერთება და საერთო წვდომის გამართვა.', 'en' => 'Connect printers and scanners to LAN/Wi-Fi and configure shared access.', 'ru' => 'Подключение принтеров и сканеров к LAN/Wi-Fi и общий доступ.']],
            ['category' => 'business-it', 'slug' => 'corporate-email-setup', 'legacy_name' => '', 'name' => ['ka' => 'კორპორაციული ელფოსტის გამართვა', 'en' => 'Corporate Email Setup', 'ru' => 'Настройка корпоративной почты'], 'description' => ['ka' => 'დომენური ელფოსტის, MX/SPF/DKIM/DMARC ჩანაწერებისა და ანგარიშების გამართვა.', 'en' => 'Domain email, MX/SPF/DKIM/DMARC records and mailbox setup.', 'ru' => 'Настройка почты домена, MX/SPF/DKIM/DMARC и почтовых ящиков.']],
            ['category' => 'business-it', 'slug' => 'microsoft-365-migration', 'legacy_name' => '', 'name' => ['ka' => 'Microsoft 365-ის გამართვა და მიგრაცია', 'en' => 'Microsoft 365 Setup and Migration', 'ru' => 'Настройка и миграция Microsoft 365'], 'description' => ['ka' => 'Microsoft 365-ის დომენის, Exchange Online-ის, მომხმარებლებისა და ფოსტის მიგრაცია.', 'en' => 'Microsoft 365 domain, Exchange Online, user setup and mail migration.', 'ru' => 'Настройка домена Microsoft 365, Exchange Online, пользователей и перенос почты.']],
            ['category' => 'business-it', 'slug' => 'remote-it-support', 'legacy_name' => '', 'name' => ['ka' => 'დისტანციური IT მხარდაჭერა', 'en' => 'Remote IT Support', 'ru' => 'Удаленная IT-поддержка'], 'description' => ['ka' => 'Windows-ის, პროგრამებისა და ელფოსტის დისტანციური გამართვა მომხმარებლის თანხმობით.', 'en' => 'Remote Windows, software and email support with user consent.', 'ru' => 'Удаленная помощь с Windows, программами и почтой с согласия клиента.']],
            ['category' => 'business-it', 'slug' => 'network-diagnostics', 'legacy_name' => '', 'name' => ['ka' => 'კომპიუტერული ქსელების დიაგნოსტიკა', 'en' => 'Computer Network Diagnostics', 'ru' => 'Диагностика компьютерных сетей'], 'description' => ['ka' => 'LAN/Wi-Fi კავშირის, როუტერებისა და ქსელური კაბელების დიაგნოსტიკა.', 'en' => 'LAN/Wi-Fi, router and network cable diagnostics.', 'ru' => 'Диагностика LAN/Wi-Fi, роутеров и сетевых кабелей.']],
            ['category' => 'business-it', 'slug' => 'backup-setup', 'legacy_name' => '', 'name' => ['ka' => 'მონაცემების სარეზერვო ასლების გამართვა', 'en' => 'Data Backup Setup', 'ru' => 'Настройка резервного копирования'], 'description' => ['ka' => 'გარე დისკზე, NAS-ზე ან ღრუბელში მონაცემების სარეზერვო კოპირება და აღდგენის შემოწმება.', 'en' => 'Backups to external drives, NAS or cloud with restore checks.', 'ru' => 'Резервное копирование на диск, NAS или в облако и проверка восстановления.']],
            ['category' => 'business-it', 'slug' => 'macos-installation', 'legacy_name' => '', 'name' => ['ka' => 'macOS-ის ინსტალაცია და კონფიგურაცია', 'en' => 'macOS Installation and Setup', 'ru' => 'Установка и настройка macOS'], 'description' => ['ka' => 'MacBook-ისა და iMac-ის macOS-ის, პროგრამებისა და ძირითადი პარამეტრების გამართვა.', 'en' => 'macOS, applications and essential settings for MacBook and iMac.', 'ru' => 'Настройка macOS, программ и параметров MacBook и iMac.']],
            ['category' => 'business-it', 'slug' => 'mac-software-setup', 'legacy_name' => '', 'name' => ['ka' => 'MacBook-ისა და iMac-ის პროგრამული გამართვა', 'en' => 'MacBook and iMac Software Setup', 'ru' => 'Программная настройка MacBook и iMac'], 'description' => ['ka' => 'MacBook-ისა და iMac-ის პროგრამული შეფერხებების დიაგნოსტიკა და გამართვა.', 'en' => 'Troubleshooting software issues on MacBook and iMac.', 'ru' => 'Диагностика и устранение программных проблем MacBook и iMac.']],
            ['category' => 'business-it', 'slug' => 'mac-app-installation', 'legacy_name' => '', 'name' => ['ka' => 'Mac-ზე პროგრამების ინსტალაცია', 'en' => 'Mac Software Installation', 'ru' => 'Установка программ на Mac'], 'description' => ['ka' => 'Mac-ზე საოფისე, სასწავლო და პროფესიული პროგრამების დაყენება და გამართვა.', 'en' => 'Install and configure office, educational and professional apps on Mac.', 'ru' => 'Установка и настройка офисных, учебных и профессиональных программ на Mac.']],
            ['category' => 'business-it', 'slug' => 'mac-diagnostics', 'legacy_name' => '', 'name' => ['ka' => 'MacBook-ისა და iMac-ის დიაგნოსტიკა', 'en' => 'MacBook and iMac Diagnostics', 'ru' => 'Диагностика MacBook и iMac'], 'description' => ['ka' => 'MacBook-ისა და iMac-ის პროგრამული, დისკისა და ქსელური მდგომარეობის დიაგნოსტიკა.', 'en' => 'Software, storage and network diagnostics for MacBook and iMac.', 'ru' => 'Диагностика ПО, диска и сети MacBook и iMac.']],
            ['category' => 'business-it', 'slug' => 'macos-update-optimization', 'legacy_name' => '', 'name' => ['ka' => 'macOS-ის განახლება და ოპტიმიზაცია', 'en' => 'macOS Updates and Optimization', 'ru' => 'Обновление и оптимизация macOS'], 'description' => ['ka' => 'macOS-ის განახლება, თავსებადობის შემოწმება და მუშაობის ოპტიმიზაცია.', 'en' => 'macOS updates, compatibility checks and performance optimization.', 'ru' => 'Обновление macOS, проверка совместимости и оптимизация.']],
            ['category' => 'business-it', 'slug' => 'mac-backup-migration', 'legacy_name' => '', 'name' => ['ka' => 'Mac-ის მონაცემების გადატანა და სარეზერვო კოპირება', 'en' => 'Mac Data Migration and Backup', 'ru' => 'Перенос данных и резервное копирование Mac'], 'description' => ['ka' => 'Mac-ის მონაცემების გადატანა, Time Machine-ის გამართვა და სარეზერვო კოპირება.', 'en' => 'Mac data transfer, Time Machine setup and backup configuration.', 'ru' => 'Перенос данных Mac, настройка Time Machine и резервных копий.']],
            ['category' => 'business-it', 'slug' => 'mac-printer-wifi-setup', 'legacy_name' => '', 'name' => ['ka' => 'Mac-ის პრინტერთან და Wi-Fi ქსელთან დაკავშირება', 'en' => 'Mac Printer and Wi-Fi Setup', 'ru' => 'Подключение Mac к принтеру и Wi-Fi'], 'description' => ['ka' => 'MacBook/iMac-ის პრინტერთან და Wi-Fi ქსელთან დაკავშირება და კავშირის ტესტირება.', 'en' => 'Connect MacBook/iMac to printers and Wi-Fi and test connectivity.', 'ru' => 'Подключение MacBook/iMac к принтерам и Wi-Fi и проверка связи.']],
            ['category' => 'telecommunications-contractor', 'slug' => 'full-low-voltage-systems', 'legacy_name' => '', 'name' => ['ka' => 'სუსტი დენების სისტემების სრული მონტაჟი', 'en' => 'Complete Low-Voltage Installation', 'ru' => 'Полный монтаж слаботочных систем'], 'description' => ['ka' => 'ქსელის, CCTV-ის, დომოფონის, სიგნალიზაციისა და დაშვების სისტემების სრული მოწყობა.', 'en' => 'Complete network, CCTV, intercom, alarm and access-system installation.', 'ru' => 'Комплексный монтаж сетей, видеонаблюдения, домофонов и контроля доступа.']],
            ['category' => 'telecommunications-contractor', 'slug' => 'structured-cabling', 'legacy_name' => '', 'name' => ['ka' => 'სტრუქტურირებული საკაბელო სისტემების მონტაჟი', 'en' => 'Structured Cabling Installation', 'ru' => 'Монтаж СКС'], 'description' => ['ka' => 'CAT6, RJ45, Patch Panel და ქსელური კარადების ერთიანი საკაბელო სისტემის მოწყობა.', 'en' => 'Structured CAT6, RJ45, patch panel and network rack installation.', 'ru' => 'Монтаж структурированной сети CAT6, RJ45, патч-панелей и шкафов.']],
            ['category' => 'telecommunications-contractor', 'slug' => 'low-voltage-new-build-renovation', 'legacy_name' => '', 'name' => ['ka' => 'სუსტი დენების გაყვანა მშენებლობისა და რემონტის ეტაპზე', 'en' => 'Low-Voltage Cabling During Construction', 'ru' => 'Слаботочная проводка при строительстве и ремонте'], 'description' => ['ka' => 'მშენებლობისა და რემონტის დროს საკაბელო მარშრუტებისა და წერტილების დაგეგმვა და გაყვანა.', 'en' => 'Plan and install low-voltage routes and outlets during building or renovation.', 'ru' => 'Планирование и прокладка слаботочных трасс при стройке и ремонте.']],
            ['category' => 'telecommunications-contractor', 'slug' => 'communications-infrastructure', 'legacy_name' => '', 'name' => ['ka' => 'საკომუნიკაციო ინფრასტრუქტურის მოწყობა', 'en' => 'Communications Infrastructure Installation', 'ru' => 'Монтаж коммуникационной инфраструктуры'], 'description' => ['ka' => 'ქსელური კაბელების, საკომუნიკაციო კარადებისა და აქტიური ქსელური მოწყობილობების მოწყობა.', 'en' => 'Network cabling, communications racks and active network equipment setup.', 'ru' => 'Монтаж сетевых кабелей, шкафов связи и сетевого оборудования.']],
            ['category' => 'telecommunications-contractor', 'slug' => 'residential-commercial-cabling', 'legacy_name' => '', 'name' => ['ka' => 'საცხოვრებელი და კომერციული ობიექტების დაკაბელება', 'en' => 'Residential and Commercial Cabling', 'ru' => 'Кабельная разводка жилых и коммерческих объектов'], 'description' => ['ka' => 'სახლების, ოფისებისა და კომერციული ობიექტების ქსელური და სუსტი დენების დაკაბელება.', 'en' => 'Network and low-voltage cabling for homes, offices and commercial sites.', 'ru' => 'Сетевая и слаботочная разводка домов, офисов и коммерческих объектов.']],
            ['category' => 'telecommunications-contractor', 'slug' => 'office-network-infrastructure', 'legacy_name' => '', 'name' => ['ka' => 'ოფისებისა და ბიზნესობიექტების ქსელური ინფრასტრუქტურა', 'en' => 'Office and Business Network Infrastructure', 'ru' => 'Сетевая инфраструктура офисов и бизнеса'], 'description' => ['ka' => 'ოფისებისა და ბიზნესობიექტების LAN/Wi-Fi ქსელის, CAT6 ხაზებისა და კომუტატორების მოწყობა.', 'en' => 'Office and business LAN/Wi-Fi, CAT6 cabling and switch configuration.', 'ru' => 'Монтаж LAN/Wi-Fi, CAT6 и коммутаторов для офисов и бизнеса.']],
        ];
    }
}