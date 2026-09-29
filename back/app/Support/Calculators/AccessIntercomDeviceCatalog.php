<?php

namespace App\Support\Calculators;

final class AccessIntercomDeviceCatalog
{
    /** @return array<string, array<string, mixed>> */
    public static function controllers(): array
    {
        return [
            'zkteco-c3-100' => [
                'brand' => 'ZKTeco', 'model' => 'C3-100', 'doors' => 1, 'voltage' => 12,
                'interfaces' => ['wiegand'], 'reader_ports' => ['wiegand' => 2],
            ],
            'zkteco-c3-200' => [
                'brand' => 'ZKTeco', 'model' => 'C3-200', 'doors' => 2, 'voltage' => 12,
                'interfaces' => ['wiegand'], 'reader_ports' => ['wiegand' => 4],
            ],
            'zkteco-c3-400' => [
                'brand' => 'ZKTeco', 'model' => 'C3-400', 'doors' => 4, 'voltage' => 12,
                'interfaces' => ['wiegand'], 'reader_ports' => ['wiegand' => 4],
            ],
            'hikvision-k2601t' => [
                'brand' => 'Hikvision', 'model' => 'DS-K2601T', 'doors' => 1, 'voltage' => 12,
                'interfaces' => ['wiegand', 'osdp', 'rs485'],
                'reader_ports' => ['wiegand' => 2, 'osdp' => 2, 'rs485' => 2],
            ],
            'hikvision-k2602t' => [
                'brand' => 'Hikvision', 'model' => 'DS-K2602T', 'doors' => 2, 'voltage' => 12,
                'interfaces' => ['wiegand', 'osdp', 'rs485'],
                'reader_ports' => ['wiegand' => 4, 'osdp' => 4, 'rs485' => 4],
            ],
            'hikvision-k2604t' => [
                'brand' => 'Hikvision', 'model' => 'DS-K2604T', 'doors' => 4, 'voltage' => 12,
                'interfaces' => ['wiegand', 'osdp', 'rs485'],
                'reader_ports' => ['wiegand' => 4, 'osdp' => 8, 'rs485' => 8],
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function readers(): array
    {
        return [
            'zkteco-kr500-e' => [
                'brand' => 'ZKTeco', 'model' => 'KR500-E', 'credential' => 'em',
                'frequency' => '125 kHz', 'interfaces' => ['wiegand'], 'voltage' => '6-14V DC',
            ],
            'zkteco-kr500-m' => [
                'brand' => 'ZKTeco', 'model' => 'KR500-M', 'credential' => 'mifare',
                'frequency' => '13.56 MHz', 'interfaces' => ['wiegand'], 'voltage' => '6-14V DC',
            ],
            'hikvision-k1108e' => [
                'brand' => 'Hikvision', 'model' => 'DS-K1108E', 'credential' => 'em',
                'frequency' => '125 kHz', 'interfaces' => ['wiegand', 'osdp', 'rs485'], 'voltage' => '12V DC',
            ],
            'hikvision-k1108m' => [
                'brand' => 'Hikvision', 'model' => 'DS-K1108M', 'credential' => 'mifare',
                'frequency' => '13.56 MHz', 'interfaces' => ['wiegand', 'osdp', 'rs485'], 'voltage' => '12V DC',
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function doorStations(): array
    {
        // Capacity comes from TVT's apartment/villa topology, not RFID card storage.
        // https://www.tvt.net.cn/keyTechnologies/index1280.html
        return [
            'tvt-td-e2223' => [
                'brand' => 'TVT', 'model' => 'TD-E2223-EM/IC/PE/WF',
                'ecosystem' => 'tvt-td', 'max_apartments' => 500, 'max_doors' => 10,
                'watts' => 12, 'poe' => 'standard', 'rfid' => true,
                'source' => 'https://www.tvt.net.cn/products/1386.html',
            ],
            'tvt-td-e3110' => [
                'brand' => 'TVT', 'model' => 'TD-E3110-IC/PE/WF',
                'ecosystem' => 'tvt-td', 'max_apartments' => 1, 'max_doors' => 10,
                'watts' => 6, 'poe' => 'standard', 'rfid' => true,
                'source' => 'https://www.tvt.net.cn/keyTechnologies/index1280.html',
            ],
            'tvt-te-vd1108' => [
                'brand' => 'TVT', 'model' => 'TE-VD1108S1-CEPW (1/2/4/8 buttons)',
                'ecosystem' => 'tvt-te', 'max_apartments' => 8,
                // Limit recommendations to one entrance until multi-entrance firmware is verified.
                'max_doors' => 1, 'watts' => 10, 'poe' => 'standard', 'rfid' => true,
                'source' => 'https://www.tvt.net.cn/products/1845.html',
            ],
            'hikvision-kv8113-wme1c' => [
                'brand' => 'Hikvision', 'model' => 'DS-KV8113-WME1(C)',
                'ecosystem' => 'hikvision-ip-intercom', 'max_apartments' => 1, 'max_doors' => 1,
                'watts' => 10, 'poe' => 'standard', 'rfid' => true,
            ],
            'hikvision-kv6113-wpe1c' => [
                'brand' => 'Hikvision', 'model' => 'DS-KV6113-WPE1(C)',
                'ecosystem' => 'hikvision-ip-intercom', 'max_apartments' => 1, 'max_doors' => 1,
                'watts' => 10, 'poe' => 'standard', 'rfid' => true,
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function indoorStations(): array
    {
        return [
            'tvt-td-e2137' => [
                'brand' => 'TVT', 'model' => 'TD-E2137-PE/TP/WF — 7"',
                'ecosystem' => 'tvt-td', 'max_per_apartment' => 6,
                'watts' => 6, 'poe' => 'standard',
                'source' => 'https://www.tvt.net.cn/products/1388.html',
            ],
            'tvt-te-vh1104' => [
                'brand' => 'TVT', 'model' => 'TE-VH1104S1-PTW — 4.3"',
                'ecosystem' => 'tvt-te', 'max_per_apartment' => 6,
                'watts' => 6, 'poe' => 'standard',
                'source' => 'https://www.tvt.net.cn/products/1900.html',
            ],
            'hikvision-kh6320-wte1' => [
                'brand' => 'Hikvision', 'model' => 'DS-KH6320-WTE1',
                'ecosystem' => 'hikvision-ip-intercom', 'max_per_apartment' => 6,
                'watts' => 6, 'poe' => 'standard',
            ],
            'hikvision-kh6320-le1b' => [
                'brand' => 'Hikvision', 'model' => 'DS-KH6320-LE1(B)',
                'ecosystem' => 'hikvision-ip-intercom', 'max_per_apartment' => 6,
                'watts' => 6, 'poe' => 'standard',
            ],
        ];
    }

    /** Generic switches are procurement requirements, not invented manufacturer models. */
    public static function switches(): array
    {
        $switches = [];
        foreach ([4 => 65, 8 => 120, 16 => 250, 24 => 370] as $ports => $budget) {
            $switches["generic-standard-poe-{$ports}"] = [
                'brand' => '', 'model' => "PoE+ {$ports} ports / ≥{$budget}W / 2 Gigabit uplinks",
                'poe' => 'standard', 'poe_ports' => $ports, 'poe_budget_w' => $budget,
                'per_port_w' => 30, 'dedicated_uplinks' => 2,
            ];
        }

        return $switches;
    }

    public static function label(array $device): string
    {
        return trim(($device['brand'] ?? '').' '.($device['model'] ?? ''));
    }

    /** @param array<string, array<string, mixed>> $devices
     * @return array<string, string>
     */
    public static function options(array $devices): array
    {
        $out = [];
        foreach ($devices as $id => $device) {
            $out[$id] = self::label($device);
        }

        return $out;
    }
}
