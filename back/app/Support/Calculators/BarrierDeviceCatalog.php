<?php

namespace App\Support\Calculators;

final class BarrierDeviceCatalog
{
    /** @return array<string, array<string, mixed>> */
    public static function barriers(): array
    {
        return [
            'zkteco-bg-m1000' => [
                'brand' => 'ZKTeco', 'model' => 'BGM1000', 'boom_types' => ['straight'],
                'boom_lengths' => [3, 4.5, 6], 'motor' => '24V DC brushless',
                'control' => ['relay', 'remote', 'software'],
            ],
            'zkteco-probg3000' => [
                'brand' => 'ZKTeco', 'model' => 'ProBG3000', 'boom_types' => ['straight'],
                'boom_lengths' => [3, 4.5, 6], 'motor' => 'servo',
                'control' => ['relay', 'remote', 'software'],
            ],
            'hikvision-tmg4b0-3m' => [
                'brand' => 'Hikvision', 'model' => 'DS-TMG4B0 3m', 'boom_types' => ['straight'],
                'boom_lengths' => [3], 'motor' => 'digital barrier',
                'control' => ['relay', 'remote', 'software'],
            ],
            'hikvision-tmg4b0-4m' => [
                'brand' => 'Hikvision', 'model' => 'DS-TMG4B0 4m', 'boom_types' => ['straight'],
                'boom_lengths' => [4], 'motor' => 'digital barrier',
                'control' => ['relay', 'remote', 'software'],
            ],
            'hikvision-tmg4b0-6m' => [
                'brand' => 'Hikvision', 'model' => 'DS-TMG4B0 6m', 'boom_types' => ['straight'],
                'boom_lengths' => [6], 'motor' => 'digital barrier',
                'control' => ['relay', 'remote', 'software'],
            ],
            'hikvision-tmg51x' => [
                'brand' => 'Hikvision', 'model' => 'DS-TMG51X Series', 'boom_types' => ['straight'],
                'boom_lengths' => [3, 4, 6], 'motor' => 'DC frequency conversion',
                'control' => ['relay', 'remote', 'software'],
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function lprCameras(): array
    {
        return [
            'zkteco-e-lprc500' => [
                'brand' => 'ZKTeco', 'model' => 'E-LPRC500', 'regions' => ['generic'],
                'trigger' => ['video'], 'relay' => true, 'network' => true,
            ],
            'zkteco-lprc300' => [
                'brand' => 'ZKTeco', 'model' => 'LPRC300', 'regions' => ['generic'],
                'trigger' => ['video', 'loop'], 'relay' => true, 'network' => true,
            ],
            'hikvision-tcg406-e' => [
                'brand' => 'Hikvision', 'model' => 'DS-TCG406-E', 'regions' => ['georgia', 'generic'],
                'trigger' => ['video', 'loop', 'radar'], 'relay' => true, 'network' => true,
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function uhfReaders(): array
    {
        return [
            'zkteco-uhf5-pro' => [
                'brand' => 'ZKTeco', 'model' => 'UHF5 Pro', 'distance' => '2–8 m',
                'interfaces' => ['wiegand', 'rs485'],
            ],
            'zkteco-uhf10-pro' => [
                'brand' => 'ZKTeco', 'model' => 'UHF10 Pro', 'distance' => '10–20 m',
                'interfaces' => ['wiegand', 'rs485'],
            ],
        ];
    }

    /** @return array<string, string> */
    public static function options(array $devices): array
    {
        $out = [];
        foreach ($devices as $id => $device) {
            $out[$id] = trim(($device['brand'] ?? '').' '.($device['model'] ?? ''));
        }

        return $out;
    }

    public static function label(array $device): string
    {
        return trim(($device['brand'] ?? '').' '.($device['model'] ?? ''));
    }
}
