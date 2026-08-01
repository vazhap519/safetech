<?php

namespace App\Support\Calculators;

final class DefaultConfiguratorComponents
{
    /** @return array<int, array<string, mixed>> */
    public static function for(string $slug, string $name = ''): array
    {
        $value = mb_strtolower(trim("{$slug} {$name}"));

        if (self::containsAny($value, ['cctv', 'camera', 'surveillance', 'video', 'კამერ', 'ვიდეო'])) {
            return self::cctv();
        }

        if (self::containsAny($value, ['network', 'wifi', 'wi-fi', 'lan', 'router', 'switch', 'ქსელ', 'ინტერნეტ'])) {
            return self::networking();
        }

        if (self::containsAny($value, ['access', 'intercom', 'door', 'gate', 'lock', 'alarm', 'ajax', 'დომოფონ', 'დაშვ', 'საკეტ', 'სიგნალიზ'])) {
            return self::accessControl();
        }

        if (self::containsAny($value, ['server', 'backup', 'virtual', 'rack', 'სერვერ', 'ბექაფ', 'ვირტუალ'])) {
            return self::serverInfrastructure();
        }

        return self::itSupport();
    }

    /** @return array<int, array<string, mixed>> */
    private static function cctv(): array
    {
        return [
            self::component(
                key: 'camera-ip-4mp-2-8',
                category: 'camera',
                titles: ['IP კამერა 4MP, 2.8mm, PoE', '4MP 2.8mm PoE IP camera', 'IP-камера 4MP, 2.8mm, PoE'],
                descriptions: ['ძირითადი კამერა არჩეული რაოდენობის მიხედვით', 'Main camera matched to the selected quantity', 'Основная камера по выбранному количеству'],
                unitPrice: 176,
                quantityMode: 'field',
                quantityField: 'camera_count',
                required: true,
                recommended: true,
                exclusiveGroup: 'camera-model',
                priority: 100,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('resolution', 'equals', '4mp'),
                    self::rule('lens', 'equals', '2.8mm'),
                ],
            ),
            self::component(
                key: 'camera-ip-2mp-2-8',
                category: 'camera',
                titles: ['IP კამერა 2MP, 2.8mm, PoE', '2MP 2.8mm PoE IP camera', 'IP-камера 2MP, 2.8mm, PoE'],
                unitPrice: 130,
                quantityMode: 'field',
                quantityField: 'camera_count',
                required: true,
                recommended: true,
                exclusiveGroup: 'camera-model',
                priority: 90,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('resolution', 'equals', '2mp'),
                    self::rule('lens', 'equals', '2.8mm'),
                ],
            ),
            self::component(
                key: 'camera-analog-2mp-2-8',
                category: 'camera',
                titles: ['ანალოგური კამერა 2MP, 2.8mm', '2MP 2.8mm analog camera', 'Аналоговая камера 2MP, 2.8mm'],
                unitPrice: 95,
                quantityMode: 'field',
                quantityField: 'camera_count',
                required: true,
                recommended: true,
                exclusiveGroup: 'camera-model',
                priority: 90,
                rules: [
                    self::rule('camera_technology', 'equals', 'analog'),
                    self::rule('resolution', 'equals', '2mp'),
                    self::rule('lens', 'equals', '2.8mm'),
                ],
            ),
            self::component(
                key: 'nvr-4ch-poe',
                category: 'recorder',
                titles: ['4-არხიანი PoE NVR', '4-channel PoE NVR', '4-канальный PoE NVR'],
                unitPrice: 145,
                required: true,
                recommended: true,
                exclusiveGroup: 'recorder',
                priority: 100,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('camera_count', 'lte', '4'),
                ],
            ),
            self::component(
                key: 'nvr-8ch-poe',
                category: 'recorder',
                titles: ['8-არხიანი PoE NVR', '8-channel PoE NVR', '8-канальный PoE NVR'],
                unitPrice: 239,
                required: true,
                recommended: true,
                exclusiveGroup: 'recorder',
                priority: 100,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('camera_count', 'gte', '5'),
                    self::rule('camera_count', 'lte', '8'),
                ],
            ),
            self::component(
                key: 'nvr-16ch-poe',
                category: 'recorder',
                titles: ['16-არხიანი PoE NVR', '16-channel PoE NVR', '16-канальный PoE NVR'],
                unitPrice: 1040,
                quantityMode: 'ceil',
                quantityField: 'camera_count',
                unitsPerComponent: 16,
                required: true,
                recommended: true,
                exclusiveGroup: 'recorder',
                priority: 100,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('camera_count', 'gte', '9'),
                ],
            ),
            self::component(
                key: 'dvr-8ch',
                category: 'recorder',
                titles: ['8-არხიანი DVR', '8-channel DVR', '8-канальный DVR'],
                unitPrice: 145,
                quantityMode: 'ceil',
                quantityField: 'camera_count',
                unitsPerComponent: 8,
                required: true,
                recommended: true,
                exclusiveGroup: 'recorder',
                priority: 100,
                rules: [self::rule('camera_technology', 'equals', 'analog')],
            ),
            self::component(
                key: 'poe-switch-8',
                category: 'network',
                titles: ['8-პორტიანი PoE სვიჩი', '8-port PoE switch', '8-портовый PoE-коммутатор'],
                unitPrice: 126,
                required: true,
                recommended: true,
                exclusiveGroup: 'poe-switch',
                priority: 90,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('camera_count', 'lte', '8'),
                ],
            ),
            self::component(
                key: 'poe-switch-16',
                category: 'network',
                titles: ['16-პორტიანი PoE სვიჩი', '16-port PoE switch', '16-портовый PoE-коммутатор'],
                unitPrice: 696,
                quantityMode: 'ceil',
                quantityField: 'camera_count',
                unitsPerComponent: 16,
                required: true,
                recommended: true,
                exclusiveGroup: 'poe-switch',
                priority: 90,
                rules: [
                    self::rule('camera_technology', 'equals', 'ip'),
                    self::rule('camera_count', 'gte', '9'),
                ],
            ),
            self::component(
                key: 'hdd-2tb',
                category: 'storage',
                titles: ['მყარი დისკი 2TB', '2TB surveillance HDD', 'Жесткий диск 2TB'],
                unitPrice: 200,
                required: true,
                recommended: true,
                exclusiveGroup: 'storage',
                priority: 80,
                rules: [
                    self::rule('camera_count', 'lte', '4'),
                    self::rule('recording_days', 'lte', '14'),
                ],
            ),
            self::component(
                key: 'hdd-4tb',
                category: 'storage',
                titles: ['მყარი დისკი 4TB', '4TB surveillance HDD', 'Жесткий диск 4TB'],
                unitPrice: 450,
                required: true,
                recommended: true,
                exclusiveGroup: 'storage',
                priority: 80,
                rules: [
                    self::rule('camera_count', 'gte', '5'),
                    self::rule('camera_count', 'lte', '8'),
                    self::rule('recording_days', 'lte', '14'),
                ],
            ),
            self::component(
                key: 'hdd-8tb',
                category: 'storage',
                titles: ['მყარი დისკი 8TB', '8TB surveillance HDD', 'Жесткий диск 8TB'],
                unitPrice: 1352,
                quantityMode: 'ceil',
                quantityField: 'camera_count',
                unitsPerComponent: 16,
                required: true,
                recommended: true,
                exclusiveGroup: 'storage',
                priority: 70,
                rules: [self::rule('camera_count', 'gte', '9')],
            ),
            self::component(
                key: 'hdd-8tb-long-retention',
                category: 'storage',
                titles: ['მყარი დისკი 8TB (გრძელი არქივი)', '8TB HDD for extended retention', 'HDD 8TB для длительного архива'],
                unitPrice: 1352,
                quantityMode: 'ceil',
                quantityField: 'recording_days',
                unitsPerComponent: 30,
                required: true,
                recommended: true,
                exclusiveGroup: 'storage',
                priority: 100,
                rules: [self::rule('recording_days', 'gte', '15')],
            ),
            self::component(
                key: 'camera-junction-box',
                category: 'accessory',
                titles: ['კამერის სამონტაჟო კოლოფი', 'Camera junction box', 'Монтажная коробка камеры'],
                unitPrice: 17,
                quantityMode: 'field',
                quantityField: 'camera_count',
                recommended: true,
                rules: [],
            ),
            self::component(
                key: 'cat6-cable',
                category: 'cabling',
                titles: ['Cat6 სპილენძის კაბელი', 'Cat6 copper cable', 'Медный кабель Cat6'],
                unitPrice: 2.5,
                quantityMode: 'field',
                quantityField: 'cable_meters',
                required: true,
                recommended: true,
                rules: [self::rule('camera_technology', 'equals', 'ip')],
            ),
            self::component(
                key: 'camera-installation',
                category: 'labor',
                titles: ['კამერის მონტაჟი და კონფიგურაცია', 'Camera installation and configuration', 'Монтаж и настройка камеры'],
                unitPrice: 150,
                quantityMode: 'field',
                quantityField: 'camera_count',
                required: true,
                recommended: true,
                rules: [],
            ),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function networking(): array
    {
        return [
            self::component('cat6-cable', 'cabling', ['Cat6 სპილენძის კაბელი', 'Cat6 copper cable', 'Медный кабель Cat6'], unitPrice: 2.2, quantityMode: 'field', quantityField: 'cable_meters', required: true, recommended: true),
            self::component('rj45-point', 'network', ['RJ45 ქსელის წერტილი', 'RJ45 network point', 'Сетевая точка RJ45'], unitPrice: 65, quantityMode: 'field', quantityField: 'network_points', required: true, recommended: true),
            self::component('patch-panel-24', 'network', ['24-პორტიანი Patch Panel', '24-port patch panel', 'Патч-панель на 24 порта'], unitPrice: 160, quantityMode: 'ceil', quantityField: 'network_points', unitsPerComponent: 24, recommended: true),
            self::component('network-installation', 'labor', ['ქსელის მონტაჟი და ტესტირება', 'Network installation and testing', 'Монтаж и тестирование сети'], unitPrice: 35, quantityMode: 'field', quantityField: 'network_points', required: true, recommended: true),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function accessControl(): array
    {
        return [
            self::component('video-intercom', 'intercom', ['ვიდეო ინტერკომი', 'Video intercom', 'Видеодомофон'], unitPrice: 550, quantityMode: 'field', quantityField: 'intercoms', recommended: true),
            self::component('electric-lock', 'lock', ['ელექტრო საკეტი', 'Electric lock', 'Электрозамок'], unitPrice: 250, quantityMode: 'field', quantityField: 'doors', required: true, recommended: true),
            self::component('backup-power', 'power', ['სარეზერვო კვება', 'Backup power supply', 'Резервное питание'], unitPrice: 220, recommended: true, rules: [self::rule('backup_power', 'truthy', '1')]),
            self::component('access-installation', 'labor', ['დაშვების სისტემის მონტაჟი', 'Access control installation', 'Монтаж системы доступа'], unitPrice: 300, quantityMode: 'field', quantityField: 'doors', required: true, recommended: true),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function serverInfrastructure(): array
    {
        return [
            self::component('server-deployment', 'server', ['სერვერის ინსტალაცია', 'Server deployment', 'Развертывание сервера'], unitPrice: 850, quantityMode: 'field', quantityField: 'servers', required: true, recommended: true),
            self::component('backup-storage', 'storage', ['სარეზერვო საცავი', 'Backup storage', 'Резервное хранилище'], unitPrice: 150, quantityMode: 'field', quantityField: 'backup_tb', recommended: true),
            self::component('server-documentation', 'labor', ['დოკუმენტაცია და ჩაბარება', 'Documentation and handover', 'Документация и сдача'], unitPrice: 250, required: true, recommended: true),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function itSupport(): array
    {
        return [
            self::component('device-service', 'labor', ['მოწყობილობის მომსახურება', 'Device service', 'Обслуживание устройства'], unitPrice: 50, quantityMode: 'field', quantityField: 'devices', required: true, recommended: true),
            self::component('onsite-hour', 'labor', ['ადგილზე სამუშაო საათი', 'On-site work hour', 'Час работы на объекте'], unitPrice: 70, quantityMode: 'field', quantityField: 'onsite_hours', recommended: true),
            self::component('remote-hour', 'labor', ['დისტანციური სამუშაო საათი', 'Remote work hour', 'Час удаленной работы'], unitPrice: 45, quantityMode: 'field', quantityField: 'remote_hours', recommended: true),
        ];
    }

    /** @param array<int, array<string, string>> $rules */
    private static function component(
        string $key,
        string $category,
        array $titles,
        array $descriptions = ['', '', ''],
        float $unitPrice = 0,
        float $monthlyPrice = 0,
        string $quantityMode = 'fixed',
        string $quantityField = '',
        float $defaultQuantity = 1,
        float $unitsPerComponent = 1,
        bool $required = false,
        bool $recommended = false,
        string $exclusiveGroup = '',
        int $priority = 0,
        array $rules = [],
    ): array {
        return [
            'key' => $key,
            'category' => $category,
            'title_ka' => $titles[0] ?? $key,
            'title_en' => $titles[1] ?? $titles[0] ?? $key,
            'title_ru' => $titles[2] ?? $titles[0] ?? $key,
            'description_ka' => $descriptions[0] ?? '',
            'description_en' => $descriptions[1] ?? '',
            'description_ru' => $descriptions[2] ?? '',
            'unit_price' => $unitPrice,
            'monthly_price' => $monthlyPrice,
            'quantity_mode' => $quantityMode,
            'quantity_field' => $quantityField,
            'default_quantity' => $defaultQuantity,
            'units_per_component' => max(1, $unitsPerComponent),
            'required' => $required,
            'recommended' => $recommended,
            'exclusive_group' => $exclusiveGroup,
            'priority' => $priority,
            'rules' => $rules,
        ];
    }

    /** @return array<string, string> */
    private static function rule(string $field, string $operator, string $value): array
    {
        return compact('field', 'operator', 'value');
    }

    /** @param array<int, string> $needles */
    private static function containsAny(string $value, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }
}
