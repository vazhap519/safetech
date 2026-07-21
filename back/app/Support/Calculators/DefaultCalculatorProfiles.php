<?php

namespace App\Support\Calculators;

final class DefaultCalculatorProfiles
{
    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        return [
            'cctv' => self::cctv(),
            'access-control' => self::accessControl(),
            'networking' => self::networking(),
            'server-infrastructure' => self::serverInfrastructure(),
            'it-support' => self::itSupport(),
        ];
    }

    /** @return array<string, mixed> */
    public static function for(string $slug): array
    {
        return self::all()[$slug] ?? self::generic();
    }

    /** @return array<string, mixed> */
    private static function cctv(): array
    {
        return self::profile(
            projectLabels: ['კამერების მასშტაბი', 'Camera scale', 'Масштаб камер'],
            projectOptions: [
                self::option('small', '1-8 კამერა', '1-8 cameras', '1-8 камер'),
                self::option('medium', '9-24 კამერა', '9-24 cameras', '9-24 камеры', 250),
                self::option('large', '25+ კამერა', '25+ cameras', '25+ камер', 600),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 250, 'minimum_price' => 500],
            fields: [
                self::field('camera_count', 'number', ['კამერების რაოდენობა', 'Number of cameras', 'Количество камер'], [
                    'required' => true, 'min' => 1, 'max' => 256, 'step' => 1, 'default' => 4,
                    'unit_price' => 220, 'unit' => ['ც', 'pcs', 'шт'],
                ]),
                self::field('recording_days', 'number', ['ჩანაწერის შენახვა', 'Recording retention', 'Срок хранения'], [
                    'min' => 1, 'max' => 180, 'step' => 1, 'default' => 30,
                    'unit' => ['დღე', 'days', 'дней'], 'unit_price' => 2,
                ]),
                self::field('camera_resolution', 'select', ['გარჩევადობა', 'Camera resolution', 'Разрешение камер'], [
                    'options' => [
                        self::option('2mp', '2MP', '2MP', '2MP'),
                        self::option('4mp', '4MP', '4MP', '4MP', 70),
                        self::option('8mp', '8MP / 4K', '8MP / 4K', '8MP / 4K', 170),
                    ],
                ]),
                self::field('cable_meters', 'number', ['კაბელის სიგრძე', 'Cable length', 'Длина кабеля'], [
                    'min' => 0, 'max' => 20000, 'step' => 1, 'default' => 100,
                    'unit' => ['მ', 'm', 'м'], 'unit_price' => 2.5,
                ]),
                self::field('floors', 'number', ['სართულების რაოდენობა', 'Number of floors', 'Количество этажей'], [
                    'min' => 1, 'max' => 100, 'step' => 1, 'default' => 1, 'unit_price' => 80,
                ]),
                self::field('rooms', 'number', ['ოთახების რაოდენობა', 'Number of rooms', 'Количество помещений'], [
                    'min' => 1, 'max' => 1000, 'step' => 1, 'default' => 4,
                ]),
                self::field('remote_monitoring', 'checkbox', ['დისტანციური მონიტორინგი', 'Remote monitoring', 'Удаленный мониторинг'], [
                    'unit_price' => 120, 'monthly_unit_price' => 25,
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
                self::option('small', '1-12 წერტილი', '1-12 points', '1-12 точек'),
                self::option('medium', '13-48 წერტილი', '13-48 points', '13-48 точек', 250),
                self::option('large', '49+ წერტილი', '49+ points', '49+ точек', 650),
            ],
            propertyLabels: ['ქსელის გარემო', 'Network environment', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 300, 'minimum_price' => 450],
            fields: [
                self::field('network_points', 'number', ['ქსელის წერტილები', 'Network points', 'Сетевые точки'], [
                    'required' => true, 'min' => 1, 'max' => 2000, 'step' => 1, 'default' => 8,
                    'unit_price' => 65, 'unit' => ['წერტილი', 'points', 'точек'],
                ]),
                self::field('cable_meters', 'number', ['კაბელის მეტრაჟი', 'Cable length', 'Метраж кабеля'], [
                    'required' => true, 'min' => 1, 'max' => 100000, 'step' => 1, 'default' => 150,
                    'unit_price' => 2.2, 'unit' => ['მ', 'm', 'м'],
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
                self::field('router_type', 'select', ['როუტერის ტიპი', 'Router type', 'Тип роутера'], [
                    'price_multiplier_field' => 'router_count',
                    'options' => [
                        self::option('basic', 'საბაზისო', 'Basic', 'Базовый', 180),
                        self::option('business', 'ბიზნეს კლასი', 'Business class', 'Бизнес-класс', 650),
                        self::option('enterprise', 'Enterprise', 'Enterprise', 'Enterprise', 1600),
                    ],
                ]),
                self::field('router_count', 'number', ['როუტერების რაოდენობა', 'Router count', 'Количество роутеров'], [
                    'min' => 0, 'max' => 100, 'step' => 1, 'default' => 1,
                ]),
                self::field('switch_type', 'select', ['სვიჩის ტიპი', 'Switch type', 'Тип коммутатора'], [
                    'price_multiplier_field' => 'switch_count',
                    'options' => [
                        self::option('unmanaged', 'არამართვადი', 'Unmanaged', 'Неуправляемый', 180),
                        self::option('managed', 'მართვადი', 'Managed', 'Управляемый', 550),
                        self::option('poe', 'Managed PoE', 'Managed PoE', 'Managed PoE', 950),
                    ],
                ]),
                self::field('switch_count', 'number', ['სვიჩების რაოდენობა', 'Switch count', 'Количество коммутаторов'], [
                    'min' => 0, 'max' => 200, 'step' => 1, 'default' => 1,
                ]),
                self::field('access_points', 'number', ['Wi-Fi წვდომის წერტილები', 'Wi-Fi access points', 'Точки доступа Wi-Fi'], [
                    'min' => 0, 'max' => 500, 'step' => 1, 'default' => 1, 'unit_price' => 320,
                ]),
                self::field('rack_type', 'select', ['Rack კარადა', 'Rack cabinet', 'Серверный шкаф'], [
                    'options' => [
                        self::option('none', 'არ არის საჭირო', 'Not required', 'Не требуется'),
                        self::option('wall', 'კედლის 6U-12U', 'Wall-mounted 6U-12U', 'Настенный 6U-12U', 450),
                        self::option('floor', 'იატაკის 18U-42U', 'Floor-standing 18U-42U', 'Напольный 18U-42U', 1200),
                    ],
                ]),
                self::field('patch_panels', 'number', ['Patch panel-ები', 'Patch panels', 'Патч-панели'], [
                    'min' => 0, 'max' => 100, 'step' => 1, 'default' => 1, 'unit_price' => 160,
                ]),
                self::field('fiber_backbone', 'checkbox', ['ოპტიკური backbone', 'Fiber backbone', 'Оптический backbone'], [
                    'unit_price' => 800,
                ]),
                self::field('floors', 'number', ['სართულების რაოდენობა', 'Number of floors', 'Количество этажей'], [
                    'min' => 1, 'max' => 100, 'step' => 1, 'default' => 1, 'unit_price' => 120,
                ]),
                self::field('rooms', 'number', ['ოთახების რაოდენობა', 'Number of rooms', 'Количество помещений'], [
                    'min' => 1, 'max' => 1000, 'step' => 1, 'default' => 6,
                ]),
            ],
            packages: [
                self::package('cabling', ['კაბელირება', 'Cabling', 'Кабельная система'], ['პასიური ქსელი და ტესტირება', 'Passive network and testing', 'Пассивная сеть и тестирование']),
                self::package('business-network', ['ბიზნეს ქსელი', 'Business network', 'Бизнес-сеть'], ['კაბელირება, მართვადი მოწყობილობები და Wi-Fi', 'Cabling, managed equipment, and Wi-Fi', 'Кабель, управляемое оборудование и Wi-Fi'], 500, 0, true),
                self::package('managed-network', ['მართვადი ქსელი', 'Managed network', 'Управляемая сеть'], ['მონიტორინგი, განახლებები და პრიორიტეტული მხარდაჭერა', 'Monitoring, updates, and priority support', 'Мониторинг, обновления и приоритетная поддержка'], 750, 180),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function accessControl(): array
    {
        return self::profile(
            projectLabels: ['წვდომის წერტილების მასშტაბი', 'Access point scale', 'Масштаб точек доступа'],
            projectOptions: [
                self::option('small', '1-4 კარი', '1-4 doors', '1-4 двери'),
                self::option('medium', '5-16 კარი', '5-16 doors', '5-16 дверей', 250),
                self::option('large', '17+ კარი', '17+ doors', '17+ дверей', 600),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL', 'base_price' => 300, 'minimum_price' => 650],
            fields: [
                self::field('door_count', 'number', ['კარების რაოდენობა', 'Door count', 'Количество дверей'], [
                    'required' => true, 'min' => 1, 'max' => 500, 'step' => 1, 'default' => 2, 'unit_price' => 480,
                ]),
                self::field('reader_type', 'select', ['იდენტიფიკაციის ტიპი', 'Reader type', 'Тип считывателя'], [
                    'options' => [
                        self::option('card', 'ბარათი', 'Card', 'Карта'),
                        self::option('pin', 'PIN + ბარათი', 'PIN + card', 'PIN + карта', 80),
                        self::option('biometric', 'ბიომეტრია', 'Biometric', 'Биометрия', 260),
                        self::option('face', 'სახის ამოცნობა', 'Face recognition', 'Распознавание лица', 520),
                    ],
                ]),
                self::field('employee_count', 'number', ['მომხმარებლების რაოდენობა', 'Number of users', 'Количество пользователей'], [
                    'min' => 1, 'max' => 100000, 'step' => 1, 'default' => 20, 'unit_price' => 2,
                ]),
                self::field('floors', 'number', ['სართულების რაოდენობა', 'Number of floors', 'Количество этажей'], [
                    'min' => 1, 'max' => 100, 'step' => 1, 'default' => 1, 'unit_price' => 80,
                ]),
                self::field('attendance', 'checkbox', ['დროის აღრიცხვა', 'Time attendance', 'Учет рабочего времени'], [
                    'unit_price' => 280, 'monthly_unit_price' => 35,
                ]),
            ],
            packages: [
                self::package('access-basic', ['საბაზისო', 'Basic', 'Базовый'], ['წვდომის კონტროლი და რეგისტრაცია', 'Access control and logging', 'Контроль доступа и журналирование']),
                self::package('access-business', ['ბიზნესი', 'Business', 'Бизнес'], ['ცენტრალიზებული მართვა და ანგარიშები', 'Central management and reporting', 'Централизованное управление и отчеты'], 400, 0, true),
                self::package('access-managed', ['მართვადი', 'Managed', 'Управляемый'], ['მხარდაჭერა და სისტემის პერიოდული შემოწმება', 'Support and scheduled system checks', 'Поддержка и плановые проверки'], 550, 95),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function serverInfrastructure(): array
    {
        return self::profile(
            projectLabels: ['ინფრასტრუქტურის მასშტაბი', 'Infrastructure size', 'Размер инфраструктуры'],
            projectOptions: [
                self::option('small', '1-2 სერვერი', '1-2 servers', '1-2 сервера'),
                self::option('medium', '3-8 სერვერი', '3-8 servers', '3-8 серверов', 600),
                self::option('large', '9+ სერვერი', '9+ servers', '9+ серверов', 1500),
            ],
            propertyLabels: ['განთავსების ტიპი', 'Deployment type', 'Тип размещения'],
            propertyOptions: [
                self::option('on-premise', 'ადგილობრივი', 'On-premise', 'Локально'),
                self::option('hybrid', 'ჰიბრიდული', 'Hybrid', 'Гибрид', 450),
                self::option('datacenter', 'Data center', 'Data center', 'Дата-центр', 900),
            ],
            pricing: ['currency' => 'GEL', 'base_price' => 800, 'minimum_price' => 1200],
            fields: [
                self::field('server_count', 'number', ['სერვერების რაოდენობა', 'Server count', 'Количество серверов'], [
                    'required' => true, 'min' => 1, 'max' => 500, 'step' => 1, 'default' => 1, 'unit_price' => 600,
                ]),
                self::field('storage_tb', 'number', ['საჭირო საცავი', 'Required storage', 'Требуемое хранилище'], [
                    'min' => 1, 'max' => 10000, 'step' => 1, 'default' => 4, 'unit_price' => 95, 'unit' => ['TB', 'TB', 'TB'],
                ]),
                self::field('rack_units', 'number', ['Rack მოცულობა', 'Rack capacity', 'Емкость стойки'], [
                    'min' => 0, 'max' => 1000, 'step' => 1, 'default' => 12, 'unit_price' => 22, 'unit' => ['U', 'U', 'U'],
                ]),
                self::field('virtualization', 'checkbox', ['ვირტუალიზაცია', 'Virtualization', 'Виртуализация'], [
                    'unit_price' => 900, 'monthly_unit_price' => 90,
                ]),
                self::field('backup_required', 'checkbox', ['Backup სისტემა', 'Backup system', 'Система резервного копирования'], [
                    'unit_price' => 650, 'monthly_unit_price' => 75,
                ]),
                self::field('monitoring', 'checkbox', ['24/7 მონიტორინგი', '24/7 monitoring', 'Мониторинг 24/7'], [
                    'unit_price' => 250, 'monthly_unit_price' => 180,
                ]),
            ],
            packages: [
                self::package('server-deploy', ['დანერგვა', 'Deployment', 'Внедрение'], ['ინსტალაცია და საბაზისო გამართვა', 'Installation and baseline configuration', 'Установка и базовая настройка']),
                self::package('server-resilient', ['მდგრადი', 'Resilient', 'Отказоустойчивый'], ['რეზერვირება, backup და დოკუმენტაცია', 'Redundancy, backup, and documentation', 'Резервирование, backup и документация'], 1200, 0, true),
                self::package('server-managed', ['მართვადი', 'Managed', 'Управляемый'], ['მონიტორინგი, განახლებები და SLA', 'Monitoring, updates, and SLA', 'Мониторинг, обновления и SLA'], 1800, 350),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function itSupport(): array
    {
        return self::profile(
            projectLabels: ['გუნდის ზომა', 'Team size', 'Размер команды'],
            projectOptions: [
                self::option('small', '1-10 თანამშრომელი', '1-10 employees', '1-10 сотрудников'),
                self::option('medium', '11-40 თანამშრომელი', '11-40 employees', '11-40 сотрудников', 0, 120),
                self::option('large', '41+ თანამშრომელი', '41+ employees', '41+ сотрудников', 0, 300),
            ],
            propertyLabels: ['მუშაობის მოდელი', 'Work model', 'Модель работы'],
            propertyOptions: [
                self::option('office', 'ოფისი', 'Office', 'Офис'),
                self::option('hybrid', 'ჰიბრიდული', 'Hybrid', 'Гибридная', 0, 60),
                self::option('remote', 'დისტანციური', 'Remote', 'Удаленная', 0, 30),
                self::option('multi-site', 'რამდენიმე ფილიალი', 'Multiple sites', 'Несколько филиалов', 0, 150),
            ],
            pricing: ['currency' => 'GEL', 'base_price' => 0, 'monthly_base_price' => 180, 'minimum_price' => 0],
            fields: [
                self::field('workstation_count', 'number', ['კომპიუტერების რაოდენობა', 'Workstation count', 'Количество компьютеров'], [
                    'required' => true, 'min' => 1, 'max' => 5000, 'step' => 1, 'default' => 5, 'monthly_unit_price' => 28,
                ]),
                self::field('server_count', 'number', ['სერვერების რაოდენობა', 'Server count', 'Количество серверов'], [
                    'min' => 0, 'max' => 500, 'step' => 1, 'default' => 0, 'monthly_unit_price' => 110,
                ]),
                self::field('visits_per_month', 'number', ['ადგილზე ვიზიტები თვეში', 'On-site visits per month', 'Выезды в месяц'], [
                    'min' => 0, 'max' => 31, 'step' => 1, 'default' => 1, 'monthly_unit_price' => 90,
                ]),
                self::field('response_time', 'select', ['რეაგირების დრო', 'Response time', 'Время реакции'], [
                    'options' => [
                        self::option('next-business-day', 'შემდეგი სამუშაო დღე', 'Next business day', 'Следующий рабочий день'),
                        self::option('8-hours', '8 სამუშაო საათი', '8 business hours', '8 рабочих часов', 0, 90),
                        self::option('4-hours', '4 სამუშაო საათი', '4 business hours', '4 рабочих часа', 0, 190),
                        self::option('1-hour', '1 საათი / კრიტიკული', '1 hour / critical', '1 час / критично', 0, 420),
                    ],
                ]),
                self::field('after_hours', 'checkbox', ['სამუშაო საათების გარეთ მხარდაჭერა', 'After-hours support', 'Поддержка вне рабочего времени'], [
                    'monthly_unit_price' => 220,
                ]),
                self::field('asset_management', 'checkbox', ['IT აქტივების მართვა', 'IT asset management', 'Учет IT-активов'], [
                    'unit_price' => 180, 'monthly_unit_price' => 60,
                ]),
            ],
            packages: [
                self::package('support-remote', ['Remote', 'Remote', 'Удаленный'], ['დისტანციური მხარდაჭერა სამუშაო საათებში', 'Remote support during business hours', 'Удаленная поддержка в рабочее время']),
                self::package('support-business', ['Business', 'Business', 'Бизнес'], ['დისტანციური და ადგილზე მხარდაჭერა', 'Remote and on-site support', 'Удаленная и выездная поддержка'], 0, 180, true),
                self::package('support-priority', ['Priority', 'Priority', 'Приоритет'], ['მოკლე SLA, მონიტორინგი და ანგარიშგება', 'Short SLA, monitoring, and reporting', 'Короткий SLA, мониторинг и отчеты'], 0, 420),
            ],
        );
    }

    /** @return array<string, mixed> */
    private static function generic(): array
    {
        return self::profile(
            projectLabels: ['პროექტის ზომა', 'Project size', 'Размер проекта'],
            projectOptions: [
                self::option('small', 'მცირე', 'Small', 'Малый'),
                self::option('medium', 'საშუალო', 'Medium', 'Средний'),
                self::option('large', 'დიდი', 'Large', 'Крупный'),
            ],
            propertyLabels: ['ობიექტის ტიპი', 'Property type', 'Тип объекта'],
            propertyOptions: self::propertyOptions(),
            pricing: ['currency' => 'GEL'],
            fields: [],
            packages: [],
        );
    }

    /** @param array<int, string> $projectLabels
     * @param  array<int, array<string, mixed>>  $projectOptions
     * @param  array<int, string>  $propertyLabels
     * @param  array<int, array<string, mixed>>  $propertyOptions
     * @param  array<string, mixed>  $pricing
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<int, array<string, mixed>>  $packages
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
            'project_size_label_ka' => $projectLabels[0],
            'project_size_label_en' => $projectLabels[1],
            'project_size_label_ru' => $projectLabels[2],
            'project_size_options' => $projectOptions,
            'property_type_label_ka' => $propertyLabels[0],
            'property_type_label_en' => $propertyLabels[1],
            'property_type_label_ru' => $propertyLabels[2],
            'property_type_options' => $propertyOptions,
            'pricing' => $pricing,
            'extra_fields' => $fields,
            'packages' => $packages,
            'calculator_disclaimer_ka' => 'ანგარიში საორიენტაციოა. საბოლოო ფასი განისაზღვრება ტექნიკური შეფასების შემდეგ.',
            'calculator_disclaimer_en' => 'The estimate is indicative. The final price is confirmed after a technical assessment.',
            'calculator_disclaimer_ru' => 'Расчет ориентировочный. Итоговая стоимость определяется после технического обследования.',
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function propertyOptions(): array
    {
        return [
            self::option('office', 'ოფისი', 'Office', 'Офис'),
            self::option('retail', 'მაღაზია / რითეილი', 'Retail', 'Ритейл'),
            self::option('hotel', 'სასტუმრო', 'Hotel', 'Отель', 150),
            self::option('warehouse', 'საწყობი', 'Warehouse', 'Склад', 180),
            self::option('residential', 'საცხოვრებელი', 'Residential', 'Жилой объект'),
            self::option('industrial', 'საწარმო', 'Industrial', 'Производство', 300),
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
        return array_filter([
            'value' => $value,
            'ka' => $ka,
            'en' => $en,
            'ru' => $ru,
            'one_time_price' => $oneTimePrice,
            'monthly_price' => $monthlyPrice,
        ], fn (mixed $value, string $key): bool => ! in_array($key, ['one_time_price', 'monthly_price'], true) || $value > 0, ARRAY_FILTER_USE_BOTH);
    }

    /** @param array<int, string> $labels
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private static function field(string $key, string $type, array $labels, array $config = []): array
    {
        $field = [
            'key' => $key,
            'type' => $type,
            'required' => false,
            'ka' => $labels[0],
            'en' => $labels[1],
            'ru' => $labels[2],
        ];

        if (is_array($config['unit'] ?? null)) {
            [$field['unit_ka'], $field['unit_en'], $field['unit_ru']] = $config['unit'];
            unset($config['unit']);
        }

        return array_merge($field, $config);
    }

    /** @param array<int, string> $titles
     * @param  array<int, string>  $descriptions
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
