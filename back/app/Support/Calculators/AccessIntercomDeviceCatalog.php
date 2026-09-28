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
        return [
            'hikvision-kv8113-wme1c' => [
                'brand' => 'Hikvision', 'model' => 'DS-KV8113-WME1(C)',
                'system' => 'ip', 'ecosystem' => 'hikvision-ip-intercom', 'poe' => 'standard',
                'rfid' => true,
            ],
            'hikvision-kv6113-wpe1c' => [
                'brand' => 'Hikvision', 'model' => 'DS-KV6113-WPE1(C)',
                'system' => 'ip', 'ecosystem' => 'hikvision-ip-intercom', 'poe' => 'standard',
                'rfid' => true,
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function indoorStations(): array
    {
        return [
            'hikvision-kh6320-wte1' => [
                'brand' => 'Hikvision', 'model' => 'DS-KH6320-WTE1',
                'system' => 'ip', 'ecosystem' => 'hikvision-ip-intercom', 'poe' => 'standard',
            ],
            'hikvision-kh6320-le1b' => [
                'brand' => 'Hikvision', 'model' => 'DS-KH6320-LE1(B)',
                'system' => 'ip', 'ecosystem' => 'hikvision-ip-intercom', 'poe' => 'standard',
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function switches(): array
    {
        return [
            'hikvision-3e0105p-em-b' => [
                'brand' => 'Hikvision', 'model' => 'DS-3E0105P-E/M(B)',
                'system' => 'ip', 'poe' => 'standard', 'poe_ports' => 4,
            ],
            'generic-standard-poe-8' => [
                'brand' => 'Generic', 'model' => '8-port IEEE 802.3af/at PoE switch',
                'system' => 'ip', 'poe' => 'standard', 'poe_ports' => 8,
            ],
            'generic-standard-poe-16' => [
                'brand' => 'Generic', 'model' => '16-port IEEE 802.3af/at PoE switch',
                'system' => 'ip', 'poe' => 'standard', 'poe_ports' => 16,
            ],
        ];
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
