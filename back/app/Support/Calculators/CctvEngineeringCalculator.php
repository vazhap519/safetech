<?php

namespace App\Support\Calculators;

/**
 * Engineering estimates, not manufacturer-certified capacity or runtime ratings.
 * Storage uses decimal TB (1 TB = 10^12 bytes) and bitrate in Mbps (10^6 bits/s).
 */
final class CctvEngineeringCalculator
{
    public function calculate(array $input): array
    {
        $days = $this->bounded($input['days'] ?? 14, 1, 365);
        $reserve = $this->bounded($input['reserve_percent'] ?? 20, 0, 100) / 100;
        $usableFraction = $this->bounded($input['disk_usable_percent'] ?? 100, 1, 100) / 100;
        $runtime = $this->bounded($input['runtime_hours'] ?? 2, 0.1, 72);
        $batteryVoltage = $this->bounded($input['battery_voltage'] ?? 12, 6, 384);
        $dod = $this->bounded($input['battery_dod_percent'] ?? 80, 10, 100) / 100;
        $inverter = $this->bounded($input['inverter_efficiency_percent'] ?? 85, 30, 100) / 100;
        $powerFactor = $this->bounded($input['ups_power_factor'] ?? 0.7, 0.4, 1);
        $headroom = $this->bounded($input['headroom_percent'] ?? 25, 0, 100) / 100;
        $groups = [];
        $storageBytes = $peakMbps = $watts = $poeWatts = $count = 0.0;
        $powerByVoltage = [];
        foreach (array_slice(is_array($input['groups'] ?? null) ? $input['groups'] : [], 0, 32) as $group) {
            if (! is_array($group)) {
                continue;
            }

            $qty = (int) $this->bounded($group['count'] ?? 0, 0, 256);
            if ($qty === 0) {
                continue;
            }

            $megapixels = $this->megapixels($group);
            $fps = $this->bounded($group['fps'] ?? 15, 1, 60);
            $codec = in_array($group['codec'] ?? '', ['h264', 'h265', 'h265plus'], true)
                ? $group['codec'] : 'h265';
            $codecFactor = ['h264' => 0.095, 'h265' => 0.06, 'h265plus' => 0.045][$codec];
            $manualBitrate = $this->bounded($group['bitrate_mbps'] ?? 0, 0, 128);
            $videoBitrate = $manualBitrate > 0
                ? $manualBitrate
                : max(0.4, $megapixels * $fps * $codecFactor);
            $audio = $this->bounded($group['audio_mbps'] ?? 0, 0, 1);
            $bitrate = $videoBitrate + $audio;
            $mode = in_array($group['mode'] ?? '', ['continuous', 'schedule', 'motion'], true)
                ? $group['mode'] : 'continuous';
            $hours = $mode === 'continuous' ? 24 : $this->bounded($group['hours_per_day'] ?? 12, 0, 24);
            $duty = $mode === 'motion'
                ? $this->bounded($group['motion_percent'] ?? 35, 0, 100) / 100
                : 1;
            $cameraWatts = $this->bounded($group['camera_watts'] ?? 8, 0, 120);
            $voltage = $this->bounded($group['voltage'] ?? 48, 5, 60);
            $poe = ($group['power_type'] ?? 'poe') === 'poe';
            // Mbps * 10^6 / 8 * 3600 * hours * days; recording duty applies only to disk.
            $groupBytes = $bitrate * 1000000 / 8 * 3600 * $hours * $days * $duty * $qty;
            $storageBytes += $groupBytes;
            $peakMbps += $bitrate * $qty;
            $watts += $cameraWatts * $qty;
            $poeWatts += $poe ? $cameraWatts * $qty : 0;
            $count += $qty;
            $key = $poe ? 'PoE (PSE)' : (string) $voltage.' V DC';
            $powerByVoltage[$key] = round(($powerByVoltage[$key] ?? 0) + $cameraWatts * $qty, 2);
            $groups[] = [
                'count' => $qty,
                'megapixels' => round($megapixels, 2),
                'codec' => $codec,
                'video_mbps_each' => round($videoBitrate, 2),
                'bitrate_source' => $manualBitrate > 0 ? 'manual' : 'estimated',
                'recording_hours_equivalent' => round($hours * $duty, 2),
                'storage_tb_raw' => round($groupBytes / 1e12, 3),
                'camera_watts_total' => round($cameraWatts * $qty, 2),
            ];
        }

        $nvr = $this->bounded($input['nvr_watts'] ?? 25, 0, 1000);
        $switch = $this->bounded($input['switch_watts'] ?? 10, 0, 1000);
        $other = $this->bounded($input['other_watts'] ?? 0, 0, 3000);
        // If a PoE switch/NVR is powered through UPS, its AC draw includes PoE output,
        // so camera watts must NOT be counted twice. Include AC conversion losses once.
        $poeEfficiency = $this->bounded($input['poe_efficiency_percent'] ?? 88, 50, 100) / 100;
        $nonPoeCameraWatts = $watts - $poeWatts;
        $acWatts = $nvr + $switch + $other + $nonPoeCameraWatts + $poeWatts / $poeEfficiency;
        $requiredTb = $storageBytes / 1e12 * (1 + $reserve) / $usableFraction;
        $batteryWh = $acWatts * $runtime / $inverter / $dod;

        return [
            'camera_count' => (int) $count,
            'groups' => $groups,
            'video_peak_mbps' => round($peakMbps, 2),
            'network_recommended_mbps' => round($peakMbps * 1.25, 2),
            'storage_raw_tb' => round($storageBytes / 1e12, 3),
            'storage_required_tb' => round($requiredTb, 3),
            'suggested_single_disk_tb' => collect([1, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20])
                ->first(fn (int $size): bool => $size >= $requiredTb),
            'poe_load_watts' => round($poeWatts, 2),
            'poe_budget_watts' => round($poeWatts * (1 + $headroom), 1),
            'power_by_voltage' => $powerByVoltage,
            'ac_load_watts' => round($acWatts, 1),
            'ups_min_output_watts' => round($acWatts * (1 + $headroom), 1),
            'ups_min_va' => (int) ceil($acWatts * (1 + $headroom) / $powerFactor),
            'battery_nominal_wh' => round($batteryWh, 1),
            'battery_nominal_ah' => round($batteryWh / $batteryVoltage, 1),
            'battery_voltage' => $batteryVoltage,
            'runtime_hours' => $runtime,
            'notes' => [
                'Bitrate not entered: estimate only. Confirm real codec, scene complexity and device bitrate.',
                'Storage includes reserve and usable-disk adjustment; confirm NVR SATA bays, maximum drive size and RAID topology.',
                'Battery Ah and UPS VA are planning estimates, not a compatible UPS model. Check the manufacturer runtime curve, battery chemistry and discharge limits.',
                'PoE cameras draw power through the switch/NVR: their consumption is counted only once in the AC estimate.',
                '12/24 V DC must match device specifications; 48 V PoE is negotiated per standard. Never wire UPS battery voltage directly to a PoE camera.',
            ],
        ];
    }

    private function bounded(mixed $value, float $min, float $max): float
    {
        return min($max, max($min, is_numeric($value) ? (float) $value : $min));
    }

    /**
     * Prefer the current MP input while retaining compatibility with payloads
     * created before the engineering calculator moved away from width × height.
     */
    private function megapixels(array $group): float
    {
        if (array_key_exists('megapixels', $group) && is_numeric($group['megapixels'])) {
            $megapixels = $this->bounded($group['megapixels'], 2, 64);

            return round($megapixels / 2) * 2;
        }

        $width = $this->bounded($group['width'] ?? 2560, 320, 16384);
        $height = $this->bounded($group['height'] ?? 1440, 240, 8640);

        return $width * $height / 1000000;
    }
}
