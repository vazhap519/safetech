<?php

namespace App\Support\Calculators;

final class DefaultCalculatorProfiles
{
    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        return [
            'cctv' => self::cctv(),
            'networking' => self::networking(),
            'access-control' => self::accessControl(),
            'server-infrastructure' => self::serverInfrastructure(),
            'it-support' => self::itSupport(),
        ];
    }

    /** @return array<string, mixed> */
    public static function for(string $slug, string $name = ''): array
    {
        $profiles = self::all();
        $value = mb_strtolower(trim("{$slug} {$name}"));

        $matches = [
            'cctv' => ['cctv', 'camera', 'surveillance', 'video', 'კამერ', 'ვიდეო'],
            'networking' => ['network', 'wi-fi', 'wifi', 'lan', 'router', 'switch', 'ქსელ', 'ინტერნეტ'],
            'access-control' => ['access', 'intercom', 'door', 'gate', 'lock', 'alarm', 'ajax', 'fire', 'დომოფონ', 'დაშვ', 'საკეტ', 'სიგნალიზ', 'სახანძრო', 'აჯაქს'],
            'server-infrastructure' => ['server', 'backup', 'virtual', 'rack', 'სერვერ', 'ბექაფ', 'ვირტუალ'],
            'it-support' => ['support', 'computer', 'printer', 'windows', 'pos', 'კომპიუტ', 'პრინტერ', 'მხარდაჭერ', 'სალარო'],
        ];

        foreach ($matches as $key => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($value, $needle)) {
                    return $profiles[$key];
                }
            }
        }

        return self::generic();
    }

    /** @return array<string, mixed> */
    private static function cctv(): array
    {
        return self::profile(
            projectLabels: ['კამერების მასშტაბი', 'Camera scale', 'Масштаб камер'],
            projectOptions: [
                self::option('small', '1–8 კამერა', '1–8 cameras', '1–8 камер'),
                self::option('medium', '9–16 კამერა', '9–16 cameras', '9–16 камер', 150),
                self::option('large', '17+ კამერა', '17+ cameras', '17+ камер', 400),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: [
                'currency' => 'GEL',
                'base_price' => 0,
                'minimum_price' => 0,
                'labor_price' => 0,
                'discount_percentage' => 0,
            ],
            fields: [
                self::field('camera_technology', 'select', ['სისტემის ტექნოლოგია', 'System technology', 'Технология системы'], [
                    'required' => true,
                    'options' => [
                        self::option('ip', 'IP / PoE', 'IP / PoE', 'IP / PoE'),
                        self::option('analog', 'ანალოგური', 'Analog', 'Аналоговая'),
                    ],
                ]),
                self::field('camera_count', 'number', ['კამერების რაოდენობა', 'Number of cameras', 'Количество камер'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 256,
                    'step' => 1,
                    'default' => 4,
                    'unit' => ['ც', 'pcs', 'шт'],
                ]),
                self::field('resolution', 'select', ['გარჩევადობა', 'Resolution', 'Разрешение'], [
                    'required' => true,
                    'options' => [
                        self::option('2mp', '2MP', '2MP', '2MP'),
                        self::option('4mp', '4MP', '4MP', '4MP'),
                        self::option('8mp', '8MP / 4K', '8MP / 4K', '8MP / 4K'),
                    ],
                ]),
                self::field('lens', 'select', ['ობიექტივი', 'Lens', 'Объектив'], [
                    'required' => true,
                    'options' => [
                        self::option('2.8mm', '2.8mm — ფართო კუთხე', '2.8mm — wide angle', '2.8mm — широкий угол'),
                        self::option('3.6mm', '3.6mm — საშუალო კუთხე', '3.6mm — medium angle', '3.6mm — средний угол'),
                        self::option('varifocal', 'ვარიაფოკალური', 'Varifocal', 'Варифокальный'),
                    ],
                ]),
                self::field('recording_days', 'number', ['ჩანაწერის შენახვა', 'Recording retention', 'Срок хранения'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 180,
                    'step' => 1,
                    'default' => 14,
                    'unit' => ['დღე', 'days', 'дней'],
                ]),
                self::field('cable_meters', 'number', ['კაბელის სიგრძე', 'Cable length', 'Длина кабеля'], [
                    'min' => 0,
                    'max' => 20000,
                    'step' => 1,
                    'default' => 100,
                    'unit' => ['მ', 'm', 'м'],
                ]),
                self::field('remote_monitoring', 'checkbox', ['დისტანციური წვდომის გამართვა', 'Remote access setup', 'Настройка удаленного доступа'], [
                    'unit_price' => 120,
                ]),
            ],
            packages: [
                self::package('standard', ['სტანდარტი', 'Standard', 'Стандарт'], ['მონტაჟი და საბაზისო გამართვა', 'Installation and basic setup', 'Монтаж и базовая настройка'], recommended: true),
                self::package('business', ['ბიზნესი', 'Business', 'Бизнес'], ['გაფართოებული გამართვა და დისტანციური წვდომა', 'Advanced setup and remote access', 'Расширенная настройка и удаленный доступ'], oneTimePrice: 250),
                self::package('managed', ['მართვადი', 'Managed', 'Управляемый'], ['პრიორიტეტული მხარდაჭერა და პერიოდული შემოწმება', 'Priority support and scheduled checks', 'Приоритетная поддержка и плановые проверки'], oneTimePrice: 350, monthlyPrice: 120),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function networking(): array
    {
        return self::profile(
            projectLabels: ['ქსელის მასშტაბი', 'Network size', 'Размер сети'],
            projectOptions: [
                self::option('small', '1–12 წერტილი', '1–12 points', '1–12 точек'),
                self::option('medium', '13–48 წერტილი', '13–48 points', '13–48 точек', 150),
                self::option('large', '49+ წერტილი', '49+ points', '49+ точек', 400),
            ],
            propertyLabels: ['ქსელის გარემო', 'Network environment', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 0, 'minimum_price' => 0, 'labor_price' => 0, 'discount_percentage' => 0],
            fields: [
                self::field('network_points', 'number', ['ქსელის წერტილები', 'Network points', 'Сетевые точки'], [
                    'required' => true, 'min' => 1, 'max' => 2000, 'step' => 1, 'default' => 8,
                ]),
                self::field('cable_meters', 'number', ['კაბელის სიგრძე', 'Cable length', 'Длина кабеля'], [
                    'required' => true, 'min' => 1, 'max' => 100000, 'step' => 1, 'default' => 150, 'unit' => ['მ', 'm', 'м'],
                ]),
                self::field('cable_type', 'select', ['კაბელის ტიპი', 'Cable type', 'Тип кабеля'], [
                    'options' => [
                        self::option('cat5e', 'Cat5e', 'Cat5e', 'Cat5e'),
                        self::option('cat6', 'Cat6', 'Cat6', 'Cat6'),
                        self::option('cat6a', 'Cat6A', 'Cat6A', 'Cat6A'),
                        self::option('fiber', 'ოპტიკური ბოჭკო', 'Fiber optic', 'Оптоволокно'),
                    ],
                ]),
                self::field('access_points', 'number', ['Wi‑Fi წვდომის წერტილები', 'Wi‑Fi access points', 'Точки доступа Wi‑Fi'], [
                    'min' => 0, 'max' => 500, 'step' => 1, 'default' => 1,
                ]),
            ],
            packages: self::standardPackages(),
        );
    }

    /** @return array<string, mixed> */
    private static function accessControl(): array
    {
        return self::profile(
            projectLabels: ['წვდომის წერტილები', 'Access points', 'Точки доступа'],
            projectOptions: [
                self::option('small', '1–2 კარი', '1–2 doors', '1–2 двери'),
                self::option('medium', '3–8 კარი', '3–8 doors', '3–8 дверей', 150),
                self::option('large', '9+ კარი', '9+ doors', '9+ дверей', 400),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 0, 'minimum_price' => 0, 'labor_price' => 0, 'discount_percentage' => 0],
            fields: [
                self::field('doors', 'number', ['კარების რაოდენობა', 'Number of doors', 'Количество дверей'], [
                    'required' => true, 'min' => 1, 'max' => 200, 'step' => 1, 'default' => 1,
                ]),
                self::field('intercoms', 'number', ['ვიდეო ინტერკომები', 'Video intercoms', 'Видеодомофоны'], [
                    'min' => 0, 'max' => 100, 'step' => 1, 'default' => 1,
                ]),
                self::field('credentials', 'number', ['ბარათები/ჩიპები', 'Cards / tags', 'Карты / брелоки'], [
                    'min' => 0, 'max' => 5000, 'step' => 1, 'default' => 10,
                ]),
                self::field('backup_power', 'checkbox', ['სარეზერვო კვება', 'Backup power', 'Резервное питание']),
            ],
            packages: self::standardPackages(),
        );
    }

    /** @return array<string, mixed> */
    private static function serverInfrastructure(): array
    {
        return self::profile(
            projectLabels: ['ინფრასტრუქტურის მასშტაბი', 'Infrastructure size', 'Масштаб инфраструктуры'],
            projectOptions: [
                self::option('small', '1 სერვერი', '1 server', '1 сервер'),
                self::option('medium', '2–4 სერვერი', '2–4 servers', '2–4 сервера', 300),
                self::option('large', '5+ სერვერი', '5+ servers', '5+ серверов', 800),
            ],
            propertyLabels: ['გარემო', 'Environment', 'Среда'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 0, 'minimum_price' => 0, 'labor_price' => 0, 'discount_percentage' => 0],
            fields: [
                self::field('servers', 'number', ['სერვერების რაოდენობა', 'Number of servers', 'Количество серверов'], [
                    'required' => true, 'min' => 1, 'max' => 100, 'step' => 1, 'default' => 1,
                ]),
                self::field('workstations', 'number', ['სამუშაო სადგურები', 'Workstations', 'Рабочие станции'], [
                    'min' => 0, 'max' => 3000, 'step' => 1, 'default' => 10,
                ]),
                self::field('backup_tb', 'number', ['სარეზერვო საცავი', 'Backup storage', 'Резервное хранилище'], [
                    'min' => 0, 'max' => 1000, 'step' => 1, 'default' => 2, 'unit' => ['TB', 'TB', 'ТБ'],
                ]),
                self::field('virtualization', 'checkbox', ['ვირტუალიზაცია', 'Virtualization', 'Виртуализация']),
            ],
            packages: self::standardPackages(),
        );
    }

    /** @return array<string, mixed> */
    private static function itSupport(): array
    {
        return self::profile(
            projectLabels: ['მომსახურების მასშტაბი', 'Support scope', 'Объем поддержки'],
            projectOptions: [
                self::option('small', '1–5 მოწყობილობა', '1–5 devices', '1–5 устройств'),
                self::option('medium', '6–20 მოწყობილობა', '6–20 devices', '6–20 устройств', 80),
                self::option('large', '21+ მოწყობილობა', '21+ devices', '21+ устройств', 200),
            ],
            propertyLabels: ['მომსახურების ტიპი', 'Support type', 'Тип поддержки'],
            propertyOptions: [
                self::option('remote', 'დისტანციური', 'Remote', 'Удаленно'),
                self::option('onsite', 'ადგილზე', 'On-site', 'На месте', 50),
                self::option('hybrid', 'ჰიბრიდული', 'Hybrid', 'Гибридный', 80),
            ],
            pricing: ['currency' => 'GEL', 'base_price' => 0, 'minimum_price' => 0, 'labor_price' => 0, 'discount_percentage' => 0],
            fields: [
                self::field('devices', 'number', ['მოწყობილობების რაოდენობა', 'Number of devices', 'Количество устройств'], [
                    'required' => true, 'min' => 1, 'max' => 3000, 'step' => 1, 'default' => 1,
                ]),
                self::field('onsite_hours', 'number', ['ადგილზე სამუშაო საათები', 'On-site hours', 'Часы на объекте'], [
                    'min' => 0, 'max' => 500, 'step' => 1, 'default' => 1, 'unit' => ['სთ', 'h', 'ч'],
                ]),
                self::field('remote_hours', 'number', ['დისტანციური სამუშაო საათები', 'Remote hours', 'Удаленные часы'], [
                    'min' => 0, 'max' => 500, 'step' => 1, 'default' => 0, 'unit' => ['სთ', 'h', 'ч'],
                ]),
                self::field('urgent', 'checkbox', ['სასწრაფო მომსახურება', 'Urgent service', 'Срочное обслуживание'], ['unit_price' => 120]),
            ],
            packages: self::standardPackages(),
        );
    }

    /** @return array<string, mixed> */
    private static function generic(): array
    {
        return self::profile(
            projectLabels: ['პროექტის მასშტაბი', 'Project size', 'Масштаб проекта'],
            projectOptions: [
                self::option('small', 'მცირე', 'Small', 'Малый'),
                self::option('medium', 'საშუალო', 'Medium', 'Средний', 100),
                self::option('large', 'დიდი', 'Large', 'Большой', 300),
            ],
            propertyLabels: ['მომსახურების ფორმატი', 'Service format', 'Формат услуги'],
            propertyOptions: [
                self::option('remote', 'დისტანციური', 'Remote', 'Удаленно'),
                self::option('onsite', 'ადგილზე', 'On-site', 'На месте', 50),
            ],
            pricing: ['currency' => 'GEL', 'base_price' => 0, 'minimum_price' => 0, 'labor_price' => 0, 'discount_percentage' => 0],
            fields: [
                self::field('devices', 'number', ['რაოდენობა', 'Quantity', 'Количество'], [
                    'required' => true, 'min' => 1, 'max' => 10000, 'step' => 1, 'default' => 1,
                ]),
                self::field('onsite_hours', 'number', ['სამუშაო საათები', 'Work hours', 'Рабочие часы'], [
                    'min' => 0, 'max' => 500, 'step' => 1, 'default' => 1, 'unit' => ['სთ', 'h', 'ч'],
                ]),
            ],
            packages: self::standardPackages(),
        );
    }

    /** @return array<int, array<string, mixed>> */
    private static function standardPackages(): array
    {
        return [
            self::package('standard', ['სტანდარტი', 'Standard', 'Стандарт'], ['საბაზისო მომსახურება', 'Core service', 'Базовое обслуживание'], recommended: true),
            self::package('priority', ['პრიორიტეტული', 'Priority', 'Приоритетный'], ['სწრაფი რეაგირება და გაფართოებული გამართვა', 'Faster response and extended setup', 'Быстрое реагирование и расширенная настройка'], oneTimePrice: 120),
            self::package('managed', ['აბონენტური', 'Managed', 'Абонентский'], ['პერიოდული მოვლა და მხარდაჭერა', 'Scheduled maintenance and support', 'Плановое обслуживание и поддержка'], oneTimePrice: 180, monthlyPrice: 150),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function propertyOptions(): array
    {
        return [
            self::option('house', 'კერძო სახლი', 'Private house', 'Частный дом'),
            self::option('office', 'ოფისი', 'Office', 'Офис', 100),
            self::option('retail', 'მაღაზია / კომერციული', 'Retail / commercial', 'Магазин / коммерческий', 150),
            self::option('hotel', 'სასტუმრო / კოტეჯი', 'Hotel / cottage', 'Отель / коттедж', 220),
            self::option('industrial', 'საწყობი / საწარმო', 'Warehouse / industrial', 'Склад / производство', 300),
        ];
    }

    /**
     * @param array{0: string, 1: string, 2: string} $projectLabels
     * @param array<int, array<string, mixed>> $projectOptions
     * @param array{0: string, 1: string, 2: string} $propertyLabels
     * @param array<int, array<string, mixed>> $propertyOptions
     * @param array<string, mixed> $pricing
     * @param array<int, array<string, mixed>> $fields
     * @param array<int, array<string, mixed>> $packages
     * @return array<string, mixed>
     */
    private static function profile(
        array $projectLabels,
        array $projectOptions,
        array $propertyLabels,
        array $propertyOptions,
        array $pricing,
        array $fields,
        array $packages,
    ): array {
        return [
            'calculator_enabled' => true,
            'pricing' => $pricing,
            'project_size_label_ka' => $projectLabels[0],
            'project_size_label_en' => $projectLabels[1],
            'project_size_label_ru' => $projectLabels[2],
            'project_size_options' => $projectOptions,
            'property_type_label_ka' => $propertyLabels[0],
            'property_type_label_en' => $propertyLabels[1],
            'property_type_label_ru' => $propertyLabels[2],
            'property_type_options' => $propertyOptions,
            'extra_fields' => $fields,
            'packages' => $packages,
            'calculator_disclaimer_ka' => 'მიღებული თანხა საორიენტაციოა. საბოლოო ფასი დგინდება ტექნიკური შეფასების შემდეგ.',
            'calculator_disclaimer_en' => 'The result is indicative. The final price is confirmed after a technical assessment.',
            'calculator_disclaimer_ru' => 'Расчет является ориентировочным. Итоговая цена подтверждается после технической оценки.',
        ];
    }

    /** @return array<string, mixed> */
    private static function option(
        string $value,
        string $ka,
        string $en,
        string $ru,
        float $oneTimePrice = 0,
        float $monthlyPrice = 0,
    ): array {
        return [
            'value' => $value,
            'ka' => $ka,
            'en' => $en,
            'ru' => $ru,
            'one_time_price' => $oneTimePrice,
            'monthly_price' => $monthlyPrice,
        ];
    }

    /**
     * @param array{0: string, 1: string, 2: string} $labels
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    private static function field(string $key, string $type, array $labels, array $settings = []): array
    {
        $field = [
            'key' => $key,
            'type' => $type,
            'ka' => $labels[0],
            'en' => $labels[1],
            'ru' => $labels[2],
        ];

        if (isset($settings['unit']) && is_array($settings['unit'])) {
            [$ka, $en, $ru] = $settings['unit'];
            $settings['unit_ka'] = $ka;
            $settings['unit_en'] = $en;
            $settings['unit_ru'] = $ru;
            unset($settings['unit']);
        }

        return array_merge($field, $settings);
    }

    /**
     * @param array{0: string, 1: string, 2: string} $titles
     * @param array{0: string, 1: string, 2: string} $descriptions
     * @return array<string, mixed>
     */
    private static function package(
        string $key,
        array $titles,
        array $descriptions,
        float $oneTimePrice = 0,
        float $monthlyPrice = 0,
        bool $recommended = false,
    ): array {
        return [
            'key' => $key,
            'title_ka' => $titles[0],
            'title_en' => $titles[1],
            'title_ru' => $titles[2],
            'description_ka' => $descriptions[0],
            'description_en' => $descriptions[1],
            'description_ru' => $descriptions[2],
            'one_time_price' => $oneTimePrice,
            'monthly_price' => $monthlyPrice,
            'recommended' => $recommended,
        ];
    }
}
