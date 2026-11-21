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
                self::component('camera', 'camera', 'IP / PoE კამერა', 'IP / PoE camera', 'IP / PoE камера', 'field', 'camera_count'),
                self::component('recorder', 'recorder', 'NVR ჩამწერი', 'NVR recorder', 'NVR регистратор', 'ceil', 'camera_count', 8),
                self::component('poe-switch', 'network', 'PoE სვიჩი', 'PoE switch', 'PoE коммутатор', 'ceil', 'camera_count', 8),
                self::component('storage', 'storage', '8TB ვიდეოარქივის დისკი', '8TB surveillance hard drive', 'Диск видеонаблюдения 8TB', 'ceil', 'required_storage_tb', 8),
                self::component('camera-cable', 'material', 'ქსელის კაბელი', 'Network cable', 'Сетевой кабель', 'field', 'cable_meters'),
                self::component('camera-installation', 'labor', 'კამერის მონტაჟი და გამართვა', 'Camera installation and setup', 'Монтаж и настройка камеры', 'field', 'camera_count'),
            ],
            'low-voltage-cabling' => [
                self::component('network-rack', 'network', 'ქსელური კარადა', 'Network rack', 'Сетевой шкаф'),
                self::component('patch-panel', 'network', 'Patch Panel', 'Patch panel', 'Патч-панель'),
                self::component('rack-accessories', 'material', 'კაბელის ორგანიზატორები და აქსესუარები', 'Cable managers and accessories', 'Кабельные организаторы и аксессуары'),
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
            'rules' => [],
        ];
    }
}
