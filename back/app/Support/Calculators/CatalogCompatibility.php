<?php

namespace App\Support\Calculators;

use App\Models\QuoteCatalogItem;

final class CatalogCompatibility
{
    /** @param array<string, mixed> $component */
    public static function apply(array $component, QuoteCatalogItem $item): array
    {
        $specs = is_array($item->technical_specs) ? $item->technical_specs : [];
        $rules = is_array($component['rules'] ?? null) ? $component['rules'] : [];

        if ($item->category === 'recorder') {
            self::appendMaximum($rules, 'camera_count', $specs['channels'] ?? null);
            self::appendMaximum($rules, 'video_bandwidth_mbps', $specs['incoming_bandwidth_mbps'] ?? null);
            self::appendMaximum($rules, 'resolution_mp', $specs['max_resolution_mp'] ?? null);
            self::appendOneOf($rules, 'recording_codec', $specs['codecs'] ?? null);
            self::appendOneOf($rules, 'storage_raid', $specs['raid_levels'] ?? null);

            $usableStorage = self::usableStorageTb($specs);
            self::appendMaximum($rules, 'required_storage_tb', $usableStorage);
        }

        if ($item->category === 'network') {
            self::appendMaximum($rules, 'camera_count', $specs['poe_ports'] ?? null);
        }

        $component['rules'] = $rules;
        $component['technicalSpecs'] = $specs;

        return $component;
    }

    /** @param array<int, array<string, mixed>> $rules */
    private static function appendMaximum(array &$rules, string $field, mixed $maximum): void
    {
        if (! is_numeric($maximum) || (float) $maximum <= 0) {
            return;
        }

        $rules[] = [
            'field' => $field,
            'operator' => 'lte',
            'value' => (string) round((float) $maximum, 3),
        ];
    }

    /** @param array<int, array<string, mixed>> $rules */
    private static function appendOneOf(array &$rules, string $field, mixed $options): void
    {
        if (! is_array($options) || $options === []) {
            return;
        }

        $values = collect($options)
            ->filter(fn ($value): bool => is_scalar($value) && trim((string) $value) !== '')
            ->map(fn ($value): string => strtolower(trim((string) $value)))
            ->unique()
            ->values()
            ->all();

        if ($values !== []) {
            $rules[] = ['field' => $field, 'operator' => 'one_of', 'value' => implode(',', $values)];
        }
    }

    /** @param array<string, mixed> $specs */
    private static function usableStorageTb(array $specs): ?float
    {
        $bays = is_numeric($specs['sata_bays'] ?? null) ? (int) $specs['sata_bays'] : 0;
        $maxDisk = is_numeric($specs['max_hdd_tb'] ?? null) ? (float) $specs['max_hdd_tb'] : 0;

        if ($bays <= 0 || $maxDisk <= 0) {
            return null;
        }

        return $bays * $maxDisk;
    }
}
