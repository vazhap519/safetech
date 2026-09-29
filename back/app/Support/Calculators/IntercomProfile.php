<?php

namespace App\Support\Calculators;

/** Shared seeded fields and bill of materials for the public and staff configurators. */
final class IntercomProfile
{
    public const VERSION = 1;

    public static function matches(string $slug, string $name = ''): bool
    {
        $text = mb_strtolower($slug.' '.$name);

        return str_contains($text, 'intercom') || str_contains($text, 'დომოფონ');
    }

    public static function catalog(): array
    {
        return [
            'doorStations' => AccessIntercomDeviceCatalog::doorStations(),
            'indoorStations' => AccessIntercomDeviceCatalog::indoorStations(),
            'switches' => AccessIntercomDeviceCatalog::switches(),
            'poeAllocationW' => 15.4,
            'poeReserve' => 1.2,
            'maxDistributionSwitches' => 47,
        ];
    }

    public static function countOptions(int $max): array
    {
        return array_map(fn (int $n): array => ['value' => (string) $n, 'ka' => (string) $n, 'en' => (string) $n, 'ru' => (string) $n], range(1, $max));
    }

    public static function fields(): array
    {
        return [
            self::field('apartments', 'select', ['ბინების / აბონენტების რაოდენობა', 'Apartments / subscribers', 'Квартиры / абоненты'], '1', ['options' => self::countOptions(100), 'required' => true, 'min' => 1, 'max' => 100]),
            self::field('doors', 'select', ['შესასვლელი კარების რაოდენობა', 'Entrance doors', 'Входные двери'], '1', ['options' => self::countOptions(10), 'required' => true, 'min' => 1, 'max' => 10]),
            self::field('monitors_per_apartment', 'select', ['მონიტორი ერთ ბინაზე', 'Monitors per apartment', 'Мониторов на квартиру'], '1', ['options' => self::countOptions(6)]),
            self::field('door_station_id', 'select', ['გარე პანელი', 'Door station', 'Вызывная панель'], 'tvt-td-e2223', ['options' => self::deviceOptions(AccessIntercomDeviceCatalog::doorStations())]),
            self::field('indoor_station_id', 'select', ['შიდა მონიტორი', 'Indoor monitor', 'Внутренний монитор'], 'tvt-td-e2137', ['options' => self::deviceOptions(AccessIntercomDeviceCatalog::indoorStations())]),
            self::field('switch_id', 'select', ['PoE სვიჩი', 'PoE switch', 'PoE-коммутатор'], 'auto', ['options' => array_merge([self::option('auto', ['ავტომატური შერჩევა', 'Automatic selection', 'Автоматический подбор'])], self::deviceOptions(AccessIntercomDeviceCatalog::switches()))]),
            self::field('lock_type', 'select', ['საკეტის ტიპი', 'Lock type', 'Тип замка'], 'maglock', ['options' => [
                self::option('maglock', ['ელექტრომაგნიტური', 'Electromagnetic', 'Электромагнитный']),
                self::option('strike', ['ელექტროსაკეტი (strike)', 'Electric strike', 'Электрозащёлка']),
                self::option('bolt', ['ელექტრორიგელი', 'Electric bolt', 'Электроригель']),
            ]]),
            self::field('exit_type', 'select', ['გასვლის ღილაკი', 'Exit button', 'Кнопка выхода'], 'button', ['options' => [
                self::option('button', ['მექანიკური NO/NC', 'Mechanical NO/NC', 'Механическая NO/NC']),
                self::option('touchless', ['უკონტაქტო NO/NC', 'Touchless NO/NC', 'Бесконтактная NO/NC']),
            ]]),
            self::field('cards_per_apartment', 'number', ['MIFARE ბარათი / ჩიპი ერთ ბინაზე', 'MIFARE cards / tags per apartment', 'Карты / брелоки MIFARE на квартиру'], 2, ['min' => 0, 'max' => 10, 'step' => 1]),
            self::field('lock_current_a', 'number', ['ერთი საკეტის დენი (A, 12V DC)', 'Current per lock (A, 12V DC)', 'Ток одного замка (А, 12 В DC)'], 0.5, ['min' => 0.1, 'max' => 5, 'step' => 0.1]),
            self::field('cable_meters', 'number', ['Cat6 კაბელი — გაზომილი სიგრძე (მ)', 'Cat6 cable — measured length (m)', 'Кабель Cat6 — измеренная длина (м)'], 0, ['min' => 0, 'max' => 100000, 'step' => 1]),
            self::field('lock_cable_meters', 'number', ['საკეტის კაბელი — გაზომილი სიგრძე (მ)', 'Lock cable — measured length (m)', 'Кабель замка — измеренная длина (м)'], 0, ['min' => 0, 'max' => 100000, 'step' => 1]),
            self::field('conduit_meters', 'number', ['გოფრა / საკაბელო არხი (მ)', 'Conduit / cable trunking (m)', 'Гофра / кабель-канал (м)'], 0, ['min' => 0, 'max' => 100000, 'step' => 1]),
            self::field('door_closer', 'checkbox', ['კარის დამხურავი', 'Door closers', 'Доводчики'], true),
            self::field('door_contact', 'checkbox', ['კარის მდგომარეობის სენსორი', 'Door position contacts', 'Датчики положения двери'], true),
            self::field('backup_power', 'checkbox', ['საკეტების აკუმულატორი და ქსელის UPS', 'Lock batteries and network UPS', 'АКБ замков и ИБП сети'], true),
            self::field('external_reader', 'checkbox', ['დამატებითი MIFARE წამკითხველი', 'Additional MIFARE reader', 'Дополнительный считыватель MIFARE'], false),
            self::field('sd_cards', 'checkbox', ['microSD მონიტორებში', 'Monitor microSD cards', 'microSD для мониторов'], false),
            self::field('surge_protection', 'checkbox', ['გარე ხაზების PoE დაცვა', 'Outdoor PoE surge protection', 'Грозозащита наружных линий PoE'], false),
            self::field('remote_access', 'checkbox', ['მობილური წვდომა / როუტერი', 'Mobile access / router', 'Мобильный доступ / маршрутизатор'], false),
        ];
    }

    public static function profile(): array
    {
        return [
            'intercom_version' => self::VERSION,
            'calculator_enabled' => true,
            'pricing' => ['currency' => 'GEL', 'base_price' => 0, 'labor_price' => 0],
            'project_size_label_ka' => 'სისტემა', 'project_size_label_en' => 'System', 'project_size_label_ru' => 'Система',
            'project_size_options' => [self::option('ip', ['IP ვიდეოდომოფონი', 'IP video intercom', 'IP-видеодомофон'])],
            'property_type_label_ka' => 'ობიექტი', 'property_type_label_en' => 'Property', 'property_type_label_ru' => 'Объект',
            'property_type_options' => [
                self::option('apartment-building', ['საცხოვრებელი კორპუსი', 'Apartment building', 'Многоквартирный дом']),
                self::option('house', ['კერძო სახლი', 'Private house', 'Частный дом']),
                self::option('office', ['ოფისი', 'Office', 'Офис']),
            ],
            'extra_fields' => self::fields(), 'components' => self::components(), 'packages' => [],
            'calculator_disclaimer_ka' => 'ბინა/აბონენტი ნიშნავს ერთ გამოძახების მისამართს. დაგეგმილია საერთო IP ქსელი და თითო პანელი თითო კარზე. კაბელის სიგრძე შეიყვანეთ გაზომვის შემდეგ. საბოლოო მოდელები, firmware, მონტაჟი და ფასი ზუსტდება ობიექტზე.',
            'calculator_disclaimer_en' => 'An apartment/subscriber is one call address. The design uses one shared IP network and one panel per door. Enter measured cable lengths. Final models, firmware, installation and pricing require a site assessment.',
            'calculator_disclaimer_ru' => 'Квартира/абонент — один адрес вызова. Расчёт для общей IP-сети и одной панели на дверь. Укажите измеренную длину кабеля. Модели, прошивки, монтаж и цена уточняются на объекте.',
        ];
    }

    public static function components(): array
    {
        $items = [];
        foreach (['door_station_id' => AccessIntercomDeviceCatalog::doorStations(), 'indoor_station_id' => AccessIntercomDeviceCatalog::indoorStations(), 'resolved_switch_id' => AccessIntercomDeviceCatalog::switches()] as $field => $devices) {
            foreach ($devices as $id => $device) {
                $name = AccessIntercomDeviceCatalog::label($device);
                $quantity = match ($field) {
                    'door_station_id' => 'doors', 'indoor_station_id' => 'monitor_count', default => 'switch_count'
                };
                $items[] = self::component($id, $field === 'resolved_switch_id' ? 'network' : 'intercom', [$name, $name, $name], $quantity, [], [self::rule($field, $id)]);
            }
        }
        foreach (['maglock' => ['12V ელექტრომაგნიტური საკეტი', '12V electromagnetic lock', 'Электромагнитный замок 12 В'], 'strike' => ['12V ელექტროსაკეტი (strike)', '12V electric strike', 'Электрозащёлка 12 В'], 'bolt' => ['12V ელექტრორიგელი', '12V electric bolt', 'Электроригель 12 В']] as $type => $title) {
            $items[] = self::component('lock-'.$type, 'lock', $title, 'doors', [], [self::rule('lock_type', $type)]);
        }
        $definitions = [
            ['panel-mount', 'accessory', ['პანელის სამონტაჟო კომპლექტი / ყუთი', 'Panel mounting kit / back box', 'Монтажный комплект / коробка панели'], 'doors', ['შეარჩიეთ პანელის ზომით; კომპლექტში არსებულს ნუ დაამატებთ მეორედ.', 'Match the panel; do not order parts already included in its box twice.', 'Подберите под панель; не дублируйте детали из её комплекта.']],
            ['monitor-mount', 'accessory', ['მონიტორის სამაგრი / ყუთი', 'Monitor bracket / back box', 'Крепление / коробка монитора'], 'monitor_count', ['შეამოწმეთ მოწყობილობის კომპლექტი.', 'Check what is included with the device.', 'Проверьте комплектацию устройства.']],
            ['lock-mount', 'accessory', ['საკეტის სამაგრი / საპასუხო ფირფიტა', 'Lock bracket / strike plate', 'Кронштейн / ответная планка замка'], 'doors', ['L/Z/U სამაგრი ან ფირფიტა კარის მასალისა და საკეტის მიხედვით.', 'L/Z/U bracket or plate to suit the door and lock.', 'Кронштейн L/Z/U или планка под дверь и замок.']],
            ['exit-button', 'accessory', ['მექანიკური გასვლის ღილაკი NO/NC', 'Mechanical NO/NC exit button', 'Механическая кнопка выхода NO/NC'], 'doors', [], [self::rule('exit_type', 'button')]],
            ['exit-touchless', 'accessory', ['უკონტაქტო გასვლის ღილაკი NO/NC', 'Touchless NO/NC exit button', 'Бесконтактная кнопка выхода NO/NC'], 'doors', [], [self::rule('exit_type', 'touchless')]],
            ['emergency-release', 'accessory', ['ავარიული განბლოკვის ღილაკი', 'Emergency door release', 'Кнопка аварийной разблокировки'], 'doors', ['Maglock-ის კვების წრედის გაწყვეტა; სახანძრო ინტეგრაცია პროექტით.', 'Interrupt the maglock supply; fire-system integration is project-specific.', 'Разрыв питания магнитного замка; интеграция с пожарной системой по проекту.'], [self::rule('lock_type', 'maglock')]],
            ['door-contact', 'accessory', ['კარის მაგნიტური კონტაქტი', 'Door position contact', 'Магнитный контакт двери'], 'doors', [], [self::rule('door_contact', '1', 'truthy')]],
            ['door-closer', 'accessory', ['კარის დამხურავი', 'Door closer', 'Доводчик'], 'doors', [], [self::rule('door_closer', '1', 'truthy')]],
            ['lock-psu', 'power', ['12V DC საკეტის კვების ბლოკი', '12V DC lock power supply', 'Блок питания замка 12 В DC'], 'doors', ['ცალკე კვება თითო კარზე, დენი +30% რეზერვი; კვება PoE პანელის რელედან არ მოდის.', 'Separate supply per door, current +30% reserve; a panel relay does not supply lock power.', 'Отдельный БП на дверь, ток +30% запаса; реле панели не питает замок.']],
            ['lock-battery', 'power', ['საკეტის კვების ბლოკის აკუმულატორი', 'Lock PSU backup battery', 'АКБ блока питания замка'], 'doors', ['ავტონომიის ხანგრძლივობით შეირჩევა ტევადობა და დამტენი.', 'Size capacity and charger for the required backup duration.', 'Ёмкость и зарядное устройство подбираются по времени автономии.'], [self::rule('backup_power', '1', 'truthy')]],
            ['network-ups', 'power', ['UPS ქსელური კვანძისთვის', 'UPS per network cabinet', 'ИБП на сетевой шкаф'], 'cabinet_count', ['PoE სვიჩი, uplink და როუტერი უნდა დარჩეს კვებაზე; სიმძლავრე/ავტონომია პროექტით.', 'Keep PoE switches, uplinks and router powered; size watts and runtime on site.', 'Питание PoE-коммутаторов, uplink и маршрутизатора; мощность и автономия по проекту.'], [self::rule('backup_power', '1', 'truthy')]],
            ['aggregation-switch', 'network', ['Gigabit აგრეგაციის სვიჩი', 'Gigabit aggregation switch', 'Гигабитный коммутатор агрегации'], 'core_count', ['თითო PoE სვიჩს ცალკე uplink; ერთი პორტი როუტერისთვის.', 'Dedicated uplink per PoE switch plus one router port.', 'Отдельный uplink на каждый PoE-коммутатор и порт маршрутизатора.']],
            ['router', 'network', ['როუტერი / Firewall', 'Router / firewall', 'Маршрутизатор / межсетевой экран'], 'router_count', ['არსებული თავსებადი როუტერის გამოყენება შესაძლებელია; ინტერნეტი ცალკეა.', 'An existing suitable router may be reused; internet service is separate.', 'Можно использовать подходящий существующий роутер; интернет оплачивается отдельно.']],
            ['cabinet', 'accessory', ['საკომუნიკაციო კარადა / PDU', 'Network cabinet / PDU', 'Сетевой шкаф / PDU'], 'cabinet_count', ['განაწილებული კვანძები; ადგილმდებარეობა და ზომა ადგილზე ზუსტდება.', 'Distributed cabinets; locations and dimensions need a site survey.', 'Распределённые шкафы; размеры и места уточняются на объекте.']],
            ['cat6', 'cabling', ['Cat6 სპილენძის კაბელი (მ)', 'Cat6 copper cable (m)', 'Медный кабель Cat6 (м)'], 'cable_meters', ['სიგრძე გაზომვით; გრძელი მონაკვეთებისთვის ოპტიკა ცალკე დაიგეგმოს.', 'Measured length; plan fiber separately for long links.', 'Длина по замеру; для длинных линий отдельно проектируется оптика.']],
            ['lock-cable', 'cabling', ['საკეტის / სენსორის კაბელი (მ)', 'Lock / sensor cable (m)', 'Кабель замка / датчика (м)'], 'lock_cable_meters', ['კვეთა ძაბვის ვარდნისა და დენის მიხედვით.', 'Conductor size based on current and voltage drop.', 'Сечение по току и падению напряжения.']],
            ['conduit', 'cabling', ['გოფრა / საკაბელო არხი (მ)', 'Conduit / trunking (m)', 'Гофра / кабель-канал (м)'], 'conduit_meters'],
            ['rj45', 'cabling', ['Cat6 RJ45 დასრულება / keystone', 'Cat6 RJ45 termination / keystone', 'Оконцевание Cat6 RJ45 / keystone'], 'termination_count'],
            ['patch-panel', 'cabling', ['24-პორტიანი Cat6 პაჩპანელი', '24-port Cat6 patch panel', 'Патч-панель Cat6 на 24 порта'], 'patch_panel_count'],
            ['consumables', 'accessory', ['სამონტაჟო მასალები / სამაგრები', 'Installation consumables / fasteners', 'Монтажные материалы / крепёж'], 'project_count'],
            ['patch-lead', 'cabling', ['Cat6 პაჩკორდი', 'Cat6 patch lead', 'Патч-корд Cat6'], 'patch_count'],
            ['labels', 'accessory', ['ხაზის მარკირების კომპლექტი', 'Cable labeling set', 'Комплект маркировки линии'], 'endpoint_count'],
            ['mifare-tags', 'accessory', ['MIFARE 13.56 MHz ბარათი / ჩიპი', 'MIFARE 13.56 MHz card / tag', 'Карта / брелок MIFARE 13,56 МГц'], 'card_count'],
            ['external-reader', 'accessory', ['გარე MIFARE Wiegand წამკითხველი', 'External MIFARE Wiegand reader', 'Внешний считыватель MIFARE Wiegand'], 'doors', ['პანელის firmware და Wiegand input ფორმატი შეამოწმეთ.', 'Verify panel firmware and Wiegand input format.', 'Проверьте прошивку панели и формат входа Wiegand.'], [self::rule('external_reader', '1', 'truthy')]],
            ['microsd', 'storage', ['microSD ბარათი ≤256GB', 'microSD card ≤256GB', 'Карта microSD ≤256 ГБ'], 'monitor_count', [], [self::rule('sd_cards', '1', 'truthy')]],
            ['poe-surge', 'accessory', ['PoE ხაზის დაცვა და დამიწება', 'PoE surge protection and earthing', 'Грозозащита PoE и заземление'], 'surge_count', [], [self::rule('surge_protection', '1', 'truthy')]],
            ['endpoint-installation', 'labor', ['პანელის / მონიტორის მონტაჟი და ტესტი', 'Panel / monitor installation and testing', 'Монтаж и проверка панели / монитора'], 'endpoint_count'],
            ['door-installation', 'labor', ['კარის აქსესუარების მონტაჟი', 'Door accessory installation', 'Монтаж дверной фурнитуры'], 'doors'],
            ['commissioning', 'labor', ['IP, ბინების, ბარათების კონფიგურაცია და ჩაბარება', 'IP, room and card configuration; handover', 'Настройка IP, квартир, карт и сдача'], 'project_count'],
        ];
        foreach ($definitions as $definition) {
            $items[] = self::component(...$definition);
        }

        return $items;
    }

    private static function component(string $key, string $category, array $labels, string $quantity, array $description = [], array $rules = []): array
    {
        return [
            'key' => $key, 'category' => $category,
            'title_ka' => $labels[0], 'title_en' => $labels[1], 'title_ru' => $labels[2],
            'description_ka' => $description[0] ?? '', 'description_en' => $description[1] ?? '', 'description_ru' => $description[2] ?? '',
            'quantity_mode' => 'field', 'quantity_field' => $quantity, 'quantity_locked' => true,
            'unit_price' => 0, 'quote_required' => true, 'required' => true, 'recommended' => true, 'rules' => $rules,
        ];
    }

    private static function rule(string $field, string $value, string $operator = 'equals'): array
    {
        return compact('field', 'value', 'operator');
    }

    private static function deviceOptions(array $devices): array
    {
        $out = [];
        foreach ($devices as $id => $device) {
            $label = AccessIntercomDeviceCatalog::label($device);
            $out[] = self::option($id, [$label, $label, $label]);
        }

        return $out;
    }

    private static function field(string $key, string $type, array $labels, mixed $default, array $settings = []): array
    {
        return array_merge(['key' => $key, 'type' => $type, 'ka' => $labels[0], 'en' => $labels[1], 'ru' => $labels[2], 'default' => $default], $settings);
    }

    private static function option(string $value, array $labels): array
    {
        return ['value' => $value, 'ka' => $labels[0], 'en' => $labels[1], 'ru' => $labels[2]];
    }
}
