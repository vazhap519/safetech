<?php

namespace App\Support\Calculators;

final class CanonicalServiceCalculator
{
    /** @return array<int, string> */
    public static function slugs(): array
    {
        return [
            'security-camera-installation',
            'low-voltage-cabling',
            'barrier-gate-installation',
            'intercom-access-control-installation',
            'business-it-support',
            'network-installation-configuration',
            'operating-system-installation',
        ];
    }

    /** @return array<string, mixed> */
    public static function for(string $slug, string $name = ''): array
    {
        if ($slug === 'barrier-gate-installation') {
            return BarrierQuoteProfile::profile();
        }

        $profile = DefaultCalculatorProfiles::for($slug, $name);
        $components = self::components($slug);
        if ($components !== []) {
            $profile['components'] = $components;
        }

        return $profile;
    }

    /** @return array<int, array<string, mixed>> */
    private static function components(string $slug): array
    {
        return match ($slug) {
            'security-camera-installation' => [
                self::component('ip-camera-2mp', 'camera', '2MP IP / PoE კამერა', '2MP IP / PoE camera', 'IP / PoE камера 2MP', 'field', 'camera_count', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('resolution', 'equals', '2mp')]),
                self::component('ip-camera-4mp', 'camera', '4MP IP / PoE კამერა', '4MP IP / PoE camera', 'IP / PoE камера 4MP', 'field', 'camera_count', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('resolution', 'equals', '4mp')]),
                self::component('ip-camera-8mp', 'camera', '8MP / 4K IP / PoE კამერა', '8MP / 4K IP / PoE camera', 'IP / PoE камера 8MP / 4K', 'field', 'camera_count', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('resolution', 'equals', '8mp')]),
                self::component('analog-camera', 'camera', 'ანალოგური HD კამერა', 'Analog HD camera', 'Аналоговая HD-камера', 'field', 'camera_count', rules: [self::rule('camera_technology', 'equals', 'analog')]),
                self::component('nvr-4ch-40mbps', 'recorder', '4-არხიანი NVR — 8MP-მდე / 40 Mbps', '4-channel NVR — up to 8MP / 40 Mbps', 'NVR 4 канала — до 8MP / 40 Mbps', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'lte', '4')], exclusiveGroup: 'recorder', priority: 100),
                self::component('nvr-8ch-80mbps', 'recorder', '8-არხიანი NVR — 8MP-მდე / 80 Mbps', '8-channel NVR — up to 8MP / 80 Mbps', 'NVR 8 каналов — до 8MP / 80 Mbps', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '5'), self::rule('camera_count', 'lte', '8')], exclusiveGroup: 'recorder', priority: 90),
                self::component('nvr-16ch-160mbps', 'recorder', '16-არხიანი NVR — 8MP-მდე / 160 Mbps', '16-channel NVR — up to 8MP / 160 Mbps', 'NVR 16 каналов — до 8MP / 160 Mbps', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '9'), self::rule('camera_count', 'lte', '16')], exclusiveGroup: 'recorder', priority: 80),
                self::component('nvr-32ch-256mbps', 'recorder', '32-არხიანი NVR — 8MP-მდე / 256 Mbps', '32-channel NVR — up to 8MP / 256 Mbps', 'NVR 32 канала — до 8MP / 256 Mbps', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '17'), self::rule('camera_count', 'lte', '32')], exclusiveGroup: 'recorder', priority: 70),
                self::component('nvr-64ch-320mbps', 'recorder', '64-არხიანი NVR — 8MP-მდე / 320 Mbps', '64-channel NVR — up to 8MP / 320 Mbps', 'NVR 64 канала — до 8MP / 320 Mbps', 'ceil', 'camera_count', 64, rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '33')], exclusiveGroup: 'recorder', priority: 60),
                self::component('dvr-8ch', 'recorder', '8-არხიანი DVR / XVR', '8-channel DVR / XVR', 'DVR / XVR 8 каналов', 'ceil', 'camera_count', 8, rules: [self::rule('camera_technology', 'equals', 'analog')], exclusiveGroup: 'recorder', priority: 50),
                self::component('poe-switch-4', 'network', '4-პორტიანი PoE სვიჩი', '4-port PoE switch', 'PoE-коммутатор 4 порта', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'lte', '4')], exclusiveGroup: 'poe', priority: 100),
                self::component('poe-switch-8', 'network', '8-პორტიანი PoE სვიჩი', '8-port PoE switch', 'PoE-коммутатор 8 портов', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '5'), self::rule('camera_count', 'lte', '8')], exclusiveGroup: 'poe', priority: 90),
                self::component('poe-switch-16', 'network', '16-პორტიანი PoE სვიჩი', '16-port PoE switch', 'PoE-коммутатор 16 портов', rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '9'), self::rule('camera_count', 'lte', '16')], exclusiveGroup: 'poe', priority: 80),
                self::component('poe-switch-24', 'network', '24-პორტიანი PoE სვიჩი', '24-port PoE switch', 'PoE-коммутатор 24 порта', 'ceil', 'camera_count', 24, rules: [self::rule('camera_technology', 'equals', 'ip'), self::rule('camera_count', 'gte', '17')], exclusiveGroup: 'poe', priority: 70),
                self::component('storage', 'storage', '8TB ვიდეოარქივის დისკი', '8TB surveillance hard drive', 'Диск видеонаблюдения 8TB', 'ceil', 'required_storage_tb', 8),
                self::component('camera-cable', 'cabling', 'ქსელის კაბელი', 'Network cable', 'Сетевой кабель', 'field', 'cable_meters'),
                self::component('camera-installation', 'labor', 'კამერის მონტაჟი და გამართვა', 'Camera installation and setup', 'Монтаж и настройка камеры', 'field', 'camera_count'),
            ],
            'low-voltage-cabling' => [
                self::component('low-voltage-cat6', 'cabling', 'CAT6 სპილენძის კაბელი — სავარაუდო მეტრაჟი', 'CAT6 copper cable — estimated length', 'Медный кабель CAT6 — расчётная длина', 'field', 'cable_meters'),
                self::component('low-voltage-point', 'material', 'სუსტი დენის წერტილი / Keystone', 'Low-voltage point / Keystone', 'Слаботочная точка / Keystone', 'field', 'network_points'),
                self::component('network-rack', 'network', 'ქსელური კარადა', 'Network rack', 'Сетевой шкаф'),
                self::component('patch-panel', 'network', '24-პორტიანი Patch Panel', '24-port patch panel', 'Патч-панель на 24 порта', 'ceil', 'network_points', 24),
                self::component('rack-accessories', 'material', 'კაბელის ორგანიზატორები და აქსესუარები', 'Cable managers and accessories', 'Кабельные организаторы и аксессуары'),
                self::component('low-voltage-installation', 'labor', 'სუსტი დენის წერტილის მონტაჟი და ტესტირება', 'Low-voltage point installation and testing', 'Монтаж и тестирование слаботочной точки', 'field', 'network_points'),
                self::component('rack-installation', 'labor', 'კარადის აწყობა, მონტაჟი და მარკირება', 'Rack assembly, installation and labeling', 'Сборка, монтаж и маркировка шкафа'),
            ],
            'network-installation-configuration' => [
                self::component('network-cable', 'material', 'CAT6 ქსელის კაბელი', 'CAT6 network cable', 'Сетевой кабель CAT6', 'field', 'cable_meters'),
                self::component('network-outlet', 'material', 'ქსელის როზეტი / Keystone', 'Network outlet / Keystone', 'Сетевая розетка / Keystone', 'field', 'network_points'),
                self::component('access-point', 'network', 'Wi-Fi წვდომის წერტილი', 'Wi-Fi access point', 'Точка доступа Wi-Fi', 'field', 'access_points'),
                self::component('network-switch', 'network', 'მართვადი ქსელის სვიჩი', 'Managed network switch', 'Управляемый сетевой коммутатор', 'ceil', 'network_points', 24),
                self::component('network-labor', 'labor', 'ქსელის წერტილის მონტაჟი და ტესტირება', 'Network point installation and testing', 'Монтаж и тестирование сетевой точки', 'field', 'network_points'),
            ],
            'business-it-support' => [
                self::component('workstation-service', 'labor', 'სამუშაო სადგურის გამართვა', 'Workstation setup', 'Настройка рабочего места', 'field', 'devices'),
                self::component('onsite-support', 'labor', 'ადგილზე IT მხარდაჭერა', 'On-site IT support', 'Выездная IT-поддержка', 'field', 'onsite_hours'),
                self::component('remote-support', 'labor', 'დისტანციური IT მხარდაჭერა', 'Remote IT support', 'Удалённая IT-поддержка', 'field', 'remote_hours'),
            ],
            'operating-system-installation' => [
                self::component('os-license', 'software', 'ოპერაციული სისტემის ლიცენზია', 'Operating system license', 'Лицензия операционной системы', 'field', 'devices'),
                self::component('os-installation-labor', 'labor', 'სისტემის ინსტალაცია და გამართვა', 'System installation and setup', 'Установка и настройка системы', 'field', 'devices'),
                self::component('data-migration', 'labor', 'მონაცემების გადატანა და სარეზერვო ასლი', 'Data migration and backup', 'Перенос и резервное копирование данных', 'field', 'devices', required: false),
            ],
            default => [],
        };
    }

    /** @return array<string, mixed> */
    private static function component(
        string $key,
        string $category,
        string $titleKa,
        string $titleEn,
        string $titleRu,
        string $quantityMode = 'fixed',
        string $quantityField = '',
        float $unitsPerComponent = 1,
        bool $required = true,
        array $rules = [],
        string $exclusiveGroup = '',
        int $priority = 0,
    ): array {
        return [
            'key' => $key,
            'category' => $category,
            'title_ka' => $titleKa,
            'title_en' => $titleEn,
            'title_ru' => $titleRu,
            'unit' => $quantityField === 'cable_meters' ? 'm' : 'pcs',
            'unit_price' => 0,
            'quantity_mode' => $quantityMode,
            'quantity_field' => $quantityField,
            'default_quantity' => 1,
            'units_per_component' => $unitsPerComponent,
            'minimum_quantity' => $required ? 1 : 0,
            'required' => $required,
            'recommended' => true,
            'exclusive_group' => $exclusiveGroup,
            'priority' => $priority,
            'rules' => $rules,
        ];
    }

    /** @return array{field: string, operator: string, value: string} */
    private static function rule(string $field, string $operator, string $value): array
    {
        return compact('field', 'operator', 'value');
    }
}
