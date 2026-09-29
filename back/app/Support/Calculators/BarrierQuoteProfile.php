<?php

namespace App\Support\Calculators;

final class BarrierQuoteProfile
{
    public const VERSION = 1;

    public static function matches(string $slug, string $name = ''): bool
    {
        $haystack = mb_strtolower($slug.' '.$name);

        return str_contains($haystack, 'barrier')
            || str_contains($haystack, 'შლაგბაუმ');
    }

    /** @return array<string, mixed> */
    public static function profile(): array
    {
        return [
            'barrier_quote_version' => self::VERSION,
            'calculator_enabled' => true,
            'pricing' => [
                'currency' => 'GEL',
                'base_price' => 0,
                'minimum_price' => 0,
                'labor_price' => 0,
                'discount_percentage' => 0,
            ],
            'project_size_label_ka' => 'სისტემის ტიპი',
            'project_size_label_en' => 'System type',
            'project_size_label_ru' => 'Тип системы',
            'project_size_options' => [
                self::option('automatic-barrier', ['ავტომატური შლაგბაუმი', 'Automatic barrier', 'Автоматический шлагбаум']),
            ],
            'property_type_label_ka' => 'ობიექტის ტიპი',
            'property_type_label_en' => 'Property type',
            'property_type_label_ru' => 'Тип объекта',
            'property_type_options' => [
                self::option('residential', ['საცხოვრებელი ეზო / კორპუსი', 'Residential yard / building', 'Жилой двор / дом']),
                self::option('business', ['ბიზნეს ობიექტი', 'Business property', 'Коммерческий объект']),
                self::option('parking', ['პარკინგი', 'Parking', 'Парковка']),
                self::option('industrial', ['სამრეწველო / საწყობი', 'Industrial / warehouse', 'Промышленный / склад']),
            ],
            'extra_fields' => self::fields(),
            'packages' => [],
            'components' => self::components(),
            'calculator_disclaimer_ka' => 'შლაგბაუმის საბოლოო მოდელი, ფუნდამენტი, კვება, უსაფრთხოების სენსორები, LPR კუთხე და კაბელების რეალური მეტრაჟი ზუსტდება ობიექტზე.',
            'calculator_disclaimer_en' => 'Final barrier model, foundation, power, safety sensors, LPR angle and actual cable lengths require a site assessment.',
            'calculator_disclaimer_ru' => 'Окончательная модель шлагбаума, основание, питание, датчики безопасности, угол LPR и фактические длины кабелей уточняются на объекте.',
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function fields(): array
    {
        return [
            self::field('lanes', 'number', ['ზოლების / შლაგბაუმების რაოდენობა', 'Lanes / barriers', 'Количество полос / шлагбаумов'], 1, [
                'min' => 1,
                'max' => 8,
                'step' => 1,
                'unit_ka' => 'ცალი',
            ]),
            self::field('boom_length', 'select', ['შტანგის სიგრძე', 'Boom length', 'Длина стрелы'], 4.5, [
                'options' => [
                    self::option('3', ['3 მ', '3 m', '3 м']),
                    self::option('4', ['4 მ', '4 m', '4 м']),
                    self::option('4.5', ['4.5 მ', '4.5 m', '4,5 м']),
                    self::option('6', ['6 მ', '6 m', '6 м']),
                ],
            ]),
            self::field('boom_type', 'select', ['შტანგის ტიპი', 'Boom type', 'Тип стрелы'], 'straight', [
                'options' => [
                    self::option('straight', ['სწორი', 'Straight', 'Прямая']),
                ],
            ]),
            self::field('access_mode', 'select', ['გახსნის მეთოდი', 'Access mode', 'Способ открытия'], 'lpr', [
                'options' => [
                    self::option('lpr', ['LPR / სანომრე ნიშნით', 'LPR / license plate', 'LPR / по номеру']),
                    self::option('uhf', ['UHF ბარათით / ტეგით', 'UHF vehicle tag', 'UHF-метка автомобиля']),
                    self::option('gsm', ['GSM / ტელეფონით', 'GSM / phone', 'GSM / по телефону']),
                    self::option('remote', ['პულტი / ღილაკი / relay', 'Remote / button / relay', 'Пульт / кнопка / реле']),
                ],
            ]),
            self::field('barrier_id', 'select', ['შლაგბაუმის მოდელი', 'Barrier model', 'Модель шлагбаума'], 'zkteco-bg-m1000', [
                'options' => self::deviceOptions(BarrierDeviceCatalog::barriers()),
            ]),
            self::field('lpr_camera_id', 'select', ['LPR კამერა', 'LPR camera', 'LPR-камера'], 'hikvision-tcg406-e', [
                'options' => self::deviceOptions(BarrierDeviceCatalog::lprCameras()),
            ]),
            self::field('vehicle_trigger', 'select', ['LPR trigger', 'LPR trigger', 'Триггер LPR'], 'video', [
                'options' => [
                    self::option('video', ['ვიდეო ანალიზი', 'Video analytics', 'Видеоаналитика']),
                    self::option('loop', ['Loop detector', 'Loop detector', 'Loop detector']),
                    self::option('radar', ['Radar', 'Radar', 'Radar']),
                ],
            ]),
            self::field('uhf_reader_id', 'select', ['UHF Reader', 'UHF reader', 'UHF-считыватель'], 'zkteco-uhf5-pro', [
                'options' => self::deviceOptions(BarrierDeviceCatalog::uhfReaders()),
            ]),
            self::field('controller_interface', 'select', ['UHF ინტერფეისი', 'UHF interface', 'Интерфейс UHF'], 'wiegand', [
                'options' => [
                    self::option('wiegand', ['Wiegand', 'Wiegand', 'Wiegand']),
                    self::option('rs485', ['RS485', 'RS485', 'RS485']),
                ],
            ]),
            self::field('tag_count', 'number', ['UHF ტეგების რაოდენობა', 'UHF tag count', 'Количество UHF-меток'], 20, [
                'min' => 1,
                'max' => 5000,
                'step' => 1,
                'unit_ka' => 'ცალი',
            ]),
            self::field('power_cable_meters', 'number', ['კვების კაბელი', 'Power cable', 'Кабель питания'], 0, [
                'min' => 0,
                'max' => 100000,
                'step' => 1,
                'unit_ka' => 'მ',
            ]),
            self::field('network_cable_meters', 'number', ['ქსელის კაბელი', 'Network cable', 'Сетевой кабель'], 0, [
                'min' => 0,
                'max' => 100000,
                'step' => 1,
                'unit_ka' => 'მ',
            ]),
            self::field('conduit_meters', 'number', ['გოფრა / კაბელარხი', 'Conduit / trunking', 'Гофра / кабель-канал'], 0, [
                'min' => 0,
                'max' => 100000,
                'step' => 1,
                'unit_ka' => 'მ',
            ]),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function components(): array
    {
        $items = [];

        foreach (BarrierDeviceCatalog::barriers() as $id => $device) {
            $items[] = self::component(
                $id,
                'accessory',
                BarrierDeviceCatalog::label($device),
                'lanes',
                [self::rule('barrier_id', $id)],
            );
        }

        foreach (BarrierDeviceCatalog::lprCameras() as $id => $device) {
            $items[] = self::component(
                $id,
                'camera',
                BarrierDeviceCatalog::label($device),
                'lanes',
                [
                    self::rule('access_mode', 'lpr'),
                    self::rule('lpr_camera_id', $id),
                ],
            );
        }

        foreach (BarrierDeviceCatalog::uhfReaders() as $id => $device) {
            $items[] = self::component(
                $id,
                'accessory',
                BarrierDeviceCatalog::label($device),
                'lanes',
                [
                    self::rule('access_mode', 'uhf'),
                    self::rule('uhf_reader_id', $id),
                ],
            );
        }

        return [
            ...$items,
            self::component('barrier-foundation', 'accessory', 'შლაგბაუმის ფუნდამენტი / სამონტაჟო ბაზა', 'lanes'),
            self::component('loop-detector', 'accessory', 'Vehicle loop detector + inductive loop', 'lanes'),
            self::component('safety-photocell', 'accessory', 'IR safety photocell pair', 'lanes'),
            self::component('lpr-network', 'network', 'PoE/network switch + LAN uplink', 'project_count', [
                self::rule('access_mode', 'lpr'),
            ]),
            self::component('gsm-relay', 'accessory', 'GSM relay / phone access module', 'lanes', [
                self::rule('access_mode', 'gsm'),
            ]),
            self::component('remote-control', 'accessory', 'Remote / wall button / relay control', 'lanes', [
                self::rule('access_mode', 'remote'),
            ]),
            self::component('uhf-tags', 'accessory', 'UHF vehicle tags', 'tag_count', [
                self::rule('access_mode', 'uhf'),
            ]),
            self::component('barrier-ups', 'power', 'UPS / backup power', 'project_count'),
            self::component('barrier-power-cable', 'cabling', 'კვების კაბელი (მ)', 'power_cable_meters'),
            self::component('barrier-network-cable', 'cabling', 'Cat6 ქსელის კაბელი (მ)', 'network_cable_meters', [
                self::rule('access_mode', 'lpr'),
            ]),
            self::component('barrier-conduit', 'cabling', 'გოფრა / კაბელარხი (მ)', 'conduit_meters'),
            self::component('barrier-installation', 'labor', 'შლაგბაუმის მონტაჟი და კონფიგურაცია', 'lanes'),
            self::component('barrier-commissioning', 'labor', 'უსაფრთხოების ტესტი და სისტემის ჩაბარება', 'project_count'),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rules
     * @return array<string, mixed>
     */
    private static function component(
        string $key,
        string $category,
        string $title,
        string $quantityField,
        array $rules = [],
    ): array {
        return [
            'key' => $key,
            'category' => $category,
            'title_ka' => $title,
            'title_en' => $title,
            'title_ru' => $title,
            'description_ka' => '',
            'description_en' => '',
            'description_ru' => '',
            'quantity_mode' => 'field',
            'quantity_field' => $quantityField,
            'quantity_locked' => true,
            'unit_price' => 0,
            'quote_required' => $category !== 'labor',
            'required' => true,
            'recommended' => true,
            'rules' => $rules,
        ];
    }

    /** @return array<string, mixed> */
    private static function rule(string $field, string $value): array
    {
        return [
            'field' => $field,
            'operator' => 'equals',
            'value' => $value,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $devices
     * @return array<int, array<string, mixed>>
     */
    private static function deviceOptions(array $devices): array
    {
        return array_map(
            fn (string $id, array $device): array => self::option(
                $id,
                array_fill(0, 3, BarrierDeviceCatalog::label($device)),
            ),
            array_keys($devices),
            array_values($devices),
        );
    }

    /**
     * @param  array{0:string,1:string,2:string}  $labels
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private static function field(
        string $key,
        string $type,
        array $labels,
        mixed $default,
        array $settings = [],
    ): array {
        return array_merge([
            'key' => $key,
            'type' => $type,
            'ka' => $labels[0],
            'en' => $labels[1],
            'ru' => $labels[2],
            'default' => $default,
        ], $settings);
    }

    /**
     * @param  array{0:string,1:string,2:string}  $labels
     * @return array<string, mixed>
     */
    private static function option(string $value, array $labels): array
    {
        return [
            'value' => $value,
            'ka' => $labels[0],
            'en' => $labels[1],
            'ru' => $labels[2],
            'one_time_price' => 0,
            'monthly_price' => 0,
        ];
    }
}
