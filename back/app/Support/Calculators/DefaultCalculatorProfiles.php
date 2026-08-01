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
        $key = self::matchKey(trim("{$slug} {$name}"));

        return $key !== null ? $profiles[$key] : self::generic();
    }

    private static function matchKey(string $value): ?string
    {
        $value = mb_strtolower($value);

        $matches = [
            'cctv' => ['cctv', 'camera', 'surveillance', 'video', 'კამერ', 'ვიდეო'],
            'networking' => ['network', 'wi-fi', 'wifi', 'lan', 'router', 'switch', 'ქსელ', 'ინტერნეტ'],
            'access-control' => [
                'access', 'intercom', 'door', 'gate', 'lock', 'alarm', 'ajax', 'fire',
                'დომოფონ', 'დაშვ', 'საკეტ', 'კარ', 'სიგნალიზ', 'სახანძრო', 'აჯაქს',
            ],
            'server-infrastructure' => ['server', 'backup', 'virtual', 'rack', 'სერვერ', 'ბექაფ', 'ვირტუალ'],
            'it-support' => ['support', 'computer', 'printer', 'windows', 'pos', 'კომპიუტ', 'პრინტერ', 'მხარდაჭერ', 'სალარო'],
        ];

        foreach ($matches as $key => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($value, $needle)) {
                    return $key;
                }
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    private static function cctv(): array
    {
        return self::profile(
            projectLabels: ['კამერების მასშტაბი', 'Camera scale', 'Масштаб камер'],
            projectOptions: [
                self::option('small', '1–8 კამერა', '1–8 cameras', '1–8 камер'),
                self::option('medium', '9–24 კამერა', '9–24 cameras', '9–24 камеры', 250),
                self::option('large', '25+ კამერა', '25+ cameras', '25+ камер', 600),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 250, 'minimum_price' => 500],
            fields: [
                self::field('camera_count', 'number', ['კამერების რაოდენობა', 'Number of cameras', 'Количество камер'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 256,
                    'step' => 1,
                    'default' => 4,
                    'unit_price' => 220,
                    'unit' => ['ც', 'pcs', 'шт'],
                ]),
                self::field('recording_days', 'number', ['ჩანაწერის შენახვა', 'Recording retention', 'Срок хранения'], [
                    'min' => 1,
                    'max' => 180,
                    'step' => 1,
                    'default' => 14,
                    'unit_price' => 2,
                    'unit' => ['დღე', 'days', 'дней'],
                ]),
                self::field('resolution', 'select', ['გარჩევადობა', 'Resolution', 'Разрешение'], [
                    'options' => [
                        self::option('2mp', '2MP', '2MP', '2MP'),
                        self::option('4mp', '4MP', '4MP', '4MP', 70),
                        self::option('8mp', '8MP / 4K', '8MP / 4K', '8MP / 4K', 170),
                    ],
                ]),
                self::field('cable_meters', 'number', ['კაბელის სიგრძე', 'Cable length', 'Длина кабеля'], [
                    'min' => 0,
                    'max' => 20000,
                    'step' => 1,
                    'default' => 100,
                    'unit_price' => 2.5,
                    'unit' => ['მ', 'm', 'м'],
                ]),
                self::field('remote_monitoring', 'checkbox', ['დისტანციური მონიტორინგი', 'Remote monitoring', 'Удаленный мониторинг'], [
                    'unit_price' => 120,
                    'monthly_unit_price' => 25,
                ]),
            ],
            packages: [
                self::package('standard', ['სტანდარტი', 'Standard', 'Стандарт'], ['მონტაჟი და საბაზისო გამართვა', 'Installation and basic setup', 'Монтаж и базовая настройка']),
                self::package('business', ['ბიზნესი', 'Business', 'Бизнес'], ['გაფართოებული გამართვა და დისტანციური წვდომა', 'Advanced setup and remote access', 'Расширенная настройка и удаленный доступ'], 350, 0, true),
                self::package('managed', ['მართვადი', 'Managed', 'Управляемый'], ['პრიორიტეტული მხარდაჭერა და პერიოდული შემოწმება', 'Priority support and scheduled checks', 'Приоритетная поддержка и плановые проверки'], 500, 120),
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
                self::option('medium', '13–48 წერტილი', '13–48 points', '13–48 точек', 250),
                self::option('large', '49+ წერტილი', '49+ points', '49+ точек', 650),
            ],
            propertyLabels: ['ქსელის გარემო', 'Network environment', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 300, 'minimum_price' => 450],
            fields: [
                self::field('network_points', 'number', ['ქსელის წერტილები', 'Network points', 'Сетевые точки'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 2000,
                    'step' => 1,
                    'default' => 8,
                    'unit_price' => 65,
                    'unit' => ['წერტილი', 'points', 'точек'],
                ]),
                self::field('cable_meters', 'number', ['კაბელის სიგრძე', 'Cable length', 'Длина кабеля'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 100000,
                    'step' => 1,
                    'default' => 150,
                    'unit_price' => 2.2,
                    'unit' => ['მ', 'm', 'м'],
                ]),
                self::field('cable_type', 'select', ['კაბელის ტიპი', 'Cable type', 'Тип кабеля'], [
                    'price_multiplier_field' => 'cable_meters',
                    'options' => [
                        self::option('cat5e', 'Cat5e', 'Cat5e', 'Cat5e'),
                        self::option('cat6', 'Cat6', 'Cat6', 'Cat6', 0.8),
                        self::option('cat6a', 'Cat6A', 'Cat6A', 'Cat6A', 1.8),
                        self::option('fiber', 'ოპტიკური ბოჭკო', 'Fiber optic', 'Оптоволокно', 5),
                    ],
                ]),
                self::field('access_points', 'number', ['Wi‑Fi წვდომის წერტილები', 'Wi‑Fi access points', 'Точки доступа Wi‑Fi'], [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 320,
                ]),
                self::field('rack', 'select', ['Rack კარადა', 'Rack cabinet', 'Серверный шкаф'], [
                    'options' => [
                        self::option('none', 'არ არის საჭირო', 'Not required', 'Не требуется'),
                        self::option('wall', 'კედლის 6U–15U', 'Wall-mounted 6U–15U', 'Настенный 6U–15U', 450),
                        self::option('floor', 'იატაკის 18U–42U', 'Floor-standing 18U–42U', 'Напольный 18U–42U', 1200),
                    ],
                ]),
            ],
            packages: [
                self::package('cabling', ['კაბელირება', 'Cabling', 'Кабельная система'], ['პასიური ქსელი და ტესტირება', 'Passive network and testing', 'Пассивная сеть и тестирование']),
                self::package('business', ['ბიზნეს ქსელი', 'Business network', 'Бизнес-сеть'], ['კაბელირება, მართვადი მოწყობილობები და Wi‑Fi', 'Cabling, managed equipment and Wi‑Fi', 'Кабельная система, управляемое оборудование и Wi‑Fi'], 600, 0, true),
                self::package('managed', ['მართვადი ქსელი', 'Managed network', 'Управляемая сеть'], ['მონიტორინგი და პრიორიტეტული მხარდაჭერა', 'Monitoring and priority support', 'Мониторинг и приоритетная поддержка'], 900, 180),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function accessControl(): array
    {
        return self::profile(
            projectLabels: ['წვდომის წერტილები', 'Access points', 'Точки доступа'],
            projectOptions: [
                self::option('small', '1–2 კარი', '1–2 doors', '1–2 двери'),
                self::option('medium', '3–8 კარი', '3–8 doors', '3–8 дверей', 250),
                self::option('large', '9+ კარი', '9+ doors', '9+ дверей', 600),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 250, 'minimum_price' => 450],
            fields: [
                self::field('doors', 'number', ['კარების რაოდენობა', 'Number of doors', 'Количество дверей'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 200,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 450,
                ]),
                self::field('intercoms', 'number', ['ვიდეო ინტერკომები', 'Video intercoms', 'Видеодомофоны'], [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 550,
                ]),
                self::field('credentials', 'number', ['ბარათები/ჩიპები', 'Cards / tags', 'Карты / брелоки'], [
                    'min' => 0,
                    'max' => 5000,
                    'step' => 1,
                    'default' => 10,
                    'unit_price' => 8,
                ]),
                self::field('biometric', 'checkbox', ['ბიომეტრიული წვდომა', 'Biometric access', 'Биометрический доступ'], [
                    'unit_price' => 350,
                ]),
                self::field('backup_power', 'checkbox', ['სარეზერვო კვება', 'Backup power', 'Резервное питание'], [
                    'unit_price' => 220,
                ]),
            ],
            packages: [
                self::package('basic', ['საბაზისო', 'Basic', 'Базовый'], ['მონტაჟი და პროგრამირება', 'Installation and programming', 'Монтаж и программирование']),
                self::package('secure', ['გაძლიერებული', 'Enhanced', 'Расширенный'], ['UPS, გასვლის ღილაკი და დეტალური კონფიგურაცია', 'UPS, exit button and advanced configuration', 'ИБП, кнопка выхода и расширенная настройка'], 350, 0, true),
                self::package('managed', ['მართვადი', 'Managed', 'Управляемый'], ['პერიოდული შემოწმება და მხარდაჭერა', 'Scheduled checks and support', 'Плановые проверки и поддержка'], 500, 100),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function serverInfrastructure(): array
    {
        return self::profile(
            projectLabels: ['ინფრასტრუქტურის მასშტაბი', 'Infrastructure size', 'Масштаб инфраструктуры'],
            projectOptions: [
                self::option('small', '1 სერვერი', '1 server', '1 сервер'),
                self::option('medium', '2–4 სერვერი', '2–4 servers', '2–4 сервера', 500),
                self::option('large', '5+ სერვერი', '5+ servers', '5+ серверов', 1400),
            ],
            propertyLabels: ['გარემო', 'Environment', 'Среда'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 600, 'minimum_price' => 900],
            fields: [
                self::field('servers', 'number', ['სერვერების რაოდენობა', 'Number of servers', 'Количество серверов'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 850,
                ]),
                self::field('workstations', 'number', ['სამუშაო სადგურები', 'Workstations', 'Рабочие станции'], [
                    'min' => 0,
                    'max' => 3000,
                    'step' => 1,
                    'default' => 10,
                    'unit_price' => 35,
                ]),
                self::field('backup_tb', 'number', ['სარეზერვო საცავი', 'Backup storage', 'Резервное хранилище'], [
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                    'default' => 2,
                    'unit_price' => 150,
                    'unit' => ['TB', 'TB', 'ТБ'],
                ]),
                self::field('virtualization', 'checkbox', ['ვირტუალიზაცია', 'Virtualization', 'Виртуализация'], [
                    'unit_price' => 700,
                ]),
                self::field('monitoring', 'checkbox', ['24/7 მონიტორინგი', '24/7 monitoring', 'Мониторинг 24/7'], [
                    'unit_price' => 250,
                    'monthly_unit_price' => 180,
                ]),
            ],
            packages: [
                self::package('deployment', ['ინსტალაცია', 'Deployment', 'Развертывание'], ['ინსტალაცია და საბაზისო გამართვა', 'Installation and basic setup', 'Установка и базовая настройка']),
                self::package('business', ['ბიზნეს', 'Business', 'Бизнес'], ['Backup, უსაფრთხოება და დოკუმენტაცია', 'Backup, security and documentation', 'Резервное копирование, безопасность и документация'], 900, 0, true),
                self::package('managed', ['მართვადი ინფრასტრუქტურა', 'Managed infrastructure', 'Управляемая инфраструктура'], ['მონიტორინგი და SLA მხარდაჭერა', 'Monitoring and SLA support', 'Мониторинг и поддержка по SLA'], 1300, 350),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function itSupport(): array
    {
        return self::profile(
            projectLabels: ['მომსახურების მასშტაბი', 'Support scope', 'Объем поддержки'],
            projectOptions: [
                self::option('small', '1–5 მოწყობილობა', '1–5 devices', '1–5 устройств'),
                self::option('medium', '6–20 მოწყობილობა', '6–20 devices', '6–20 устройств', 120),
                self::option('large', '21+ მოწყობილობა', '21+ devices', '21+ устройств', 350),
            ],
            propertyLabels: ['მომსახურების ტიპი', 'Support type', 'Тип поддержки'],
            propertyOptions: [
                self::option('remote', 'დისტანციური', 'Remote', 'Удаленно'),
                self::option('onsite', 'ადგილზე', 'On-site', 'На месте', 50),
                self::option('hybrid', 'ჰიბრიდული', 'Hybrid', 'Гибридный', 80),
            ],
            pricing: ['currency' => 'GEL', 'base_price' => 80, 'minimum_price' => 120],
            fields: [
                self::field('devices', 'number', ['მოწყობილობების რაოდენობა', 'Number of devices', 'Количество устройств'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 3000,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 50,
                ]),
                self::field('onsite_hours', 'number', ['ადგილზე სამუშაო საათები', 'On-site hours', 'Часы на объекте'], [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 70,
                    'unit' => ['სთ', 'h', 'ч'],
                ]),
                self::field('remote_hours', 'number', ['დისტანციური სამუშაო საათები', 'Remote hours', 'Удаленные часы'], [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 0,
                    'unit_price' => 45,
                    'unit' => ['სთ', 'h', 'ч'],
                ]),
                self::field('urgent', 'checkbox', ['სასწრაფო მომსახურება', 'Urgent service', 'Срочное обслуживание'], [
                    'unit_price' => 120,
                ]),
                self::field('monthly_support', 'checkbox', ['ყოველთვიური მხარდაჭერა', 'Monthly support', 'Ежемесячная поддержка'], [
                    'monthly_unit_price' => 150,
                ]),
            ],
            packages: [
                self::package('one-time', ['ერთჯერადი', 'One-time', 'Разово'], ['დიაგნოსტიკა და სამუშაო', 'Diagnostics and service', 'Диагностика и обслуживание']),
                self::package('priority', ['პრიორიტეტული', 'Priority', 'Приоритетный'], ['სწრაფი რეაგირება და გაფართოებული სამუშაო', 'Faster response and extended service', 'Быстрое реагирование и расширенное обслуживание'], 120, 0, true),
                self::package('contract', ['აბონენტური', 'Managed contract', 'Абонентский договор'], ['გეგმური მოვლა და მხარდაჭერა', 'Planned maintenance and support', 'Плановое обслуживание и поддержка'], 180, 220),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function generic(): array
    {
        return self::profile(
            projectLabels: ['პროექტის მასშტაბი', 'Project size', 'Масштаб проекта'],
            projectOptions: [
                self::option('small', 'მცირე', 'Small', 'Малый'),
                self::option('medium', 'საშუალო', 'Medium', 'Средний', 150),
                self::option('large', 'დიდი', 'Large', 'Большой', 400),
            ],
            propertyLabels: ['მომსახურების ფორმატი', 'Service format', 'Формат услуги'],
            propertyOptions: [
                self::option('remote', 'დისტანციური', 'Remote', 'Удаленно'),
                self::option('onsite', 'ადგილზე', 'On-site', 'На месте', 50),
            ],
            pricing: ['currency' => 'GEL', 'base_price' => 100, 'minimum_price' => 150],
            fields: [
                self::field('hours', 'number', ['სამუშაო საათები', 'Work hours', 'Рабочие часы'], [
                    'required' => true,
                    'min' => 1,
                    'max' => 500,
                    'step' => 1,
                    'default' => 1,
                    'unit_price' => 70,
                    'unit' => ['სთ', 'h', 'ч'],
                ]),
                self::field('visits', 'number', ['ადგილზე ვიზიტები', 'On-site visits', 'Выезды на объект'], [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'default' => 0,
                    'unit_price' => 40,
                ]),
            ],
            packages: [
                self::package('standard', ['სტანდარტი', 'Standard', 'Стандарт'], ['საბაზისო მომსახურება', 'Basic service', 'Базовое обслуживание'], 0, 0, true),
                self::package('priority', ['პრიორიტეტული', 'Priority', 'Приоритетный'], ['სწრაფი რეაგირება', 'Faster response', 'Быстрое реагирование'], 120),
            ],
        );
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
