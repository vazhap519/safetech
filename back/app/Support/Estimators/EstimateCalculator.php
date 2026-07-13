<?php

namespace App\Support\Estimators;

class EstimateCalculator
{
    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function calculate(array $input): array
    {
        $projectType = (string) ($input['project_type'] ?? 'cctv');
        $cameraType = (string) ($input['camera_type'] ?? 'ip');
        $usesCctvHardware = $projectType === 'cctv';
        $cameraCount = $this->toInt($input['camera_count'] ?? 0);
        $megapixels = $this->toFloat($input['camera_megapixels'] ?? 4);
        $framesPerSecond = $this->toInt($input['frames_per_second'] ?? 15);
        $recordingHours = $this->toInt($input['recording_hours_per_day'] ?? 24);
        $recordingDays = $this->toInt($input['recording_days'] ?? 30);
        $cameraUnitCost = $this->toFloat($input['camera_unit_cost'] ?? 0);
        $recorderUnitCost = $this->toFloat($input['recorder_unit_cost'] ?? 0);
        $cableMeters = $this->toFloat($input['cable_meters'] ?? 0);
        $cableUnitCost = $this->toFloat($input['cable_unit_cost'] ?? 0);
        $trunkingMeters = $this->toFloat($input['trunking_meters'] ?? 0);
        $trunkingUnitCost = $this->toFloat($input['trunking_unit_cost'] ?? 0);
        $installationCost = $this->toFloat($input['installation_cost'] ?? 0);
        $hddCostPerTb = $this->toFloat($input['hdd_cost_per_tb'] ?? 0);
        $poeSwitchUnitCost = $this->toFloat($input['poe_switch_unit_cost'] ?? 0);
        $connectorUnitCost = $this->toFloat($input['connector_unit_cost'] ?? 0);
        $markupRate = max(0, $this->toFloat($input['markup_rate'] ?? 0.6));
        $manualItems = is_array($input['manual_items'] ?? null) ? $input['manual_items'] : [];
        $sellMultiplier = 1 + $markupRate;

        $recorderChannels = $usesCctvHardware
            ? $this->recommendedRecorderChannels($cameraCount)
            : 0;
        $requiredStorageTb = $this->requiredStorageTb(
            $cameraCount,
            $cameraType,
            $megapixels,
            $framesPerSecond,
            $recordingHours,
            $recordingDays,
            $projectType,
        );

        $drivePlan = $this->recommendedDrives($requiredStorageTb, $projectType);
        $poePlan = $this->recommendedPoeSwitches($cameraCount, $cameraType, $projectType);
        $connectorCount = $this->connectorCount($cameraCount, $projectType);
        $connectorLabel = $cameraType === 'analog' ? 'BNC Connector' : 'RJ45 Connector';

        $lineItems = [];
        if ($usesCctvHardware) {
            $lineItems[] = $this->lineItem(
                key: 'cameras',
                label: $cameraType === 'analog' ? 'Analog Camera' : 'IP Camera',
                quantity: $cameraCount,
                unit: 'pcs',
                unitCost: $cameraUnitCost,
                sellMultiplier: $sellMultiplier,
            );

            $lineItems[] = $this->lineItem(
                key: 'recorder',
                label: $recorderChannels > 0
                    ? sprintf(
                        '%d-channel %s',
                        $recorderChannels,
                        $cameraType === 'analog' ? 'DVR' : 'NVR',
                    )
                    : ($cameraType === 'analog' ? 'DVR' : 'NVR'),
                quantity: $cameraCount > 0 ? 1 : 0,
                unit: 'pcs',
                unitCost: $recorderUnitCost,
                sellMultiplier: $sellMultiplier,
            );

            foreach ($drivePlan as $index => $drive) {
                $lineItems[] = $this->lineItem(
                    key: "hdd_{$index}",
                    label: sprintf('Surveillance HDD %d TB', $drive['size_tb']),
                    quantity: $drive['quantity'],
                    unit: 'pcs',
                    unitCost: $hddCostPerTb * $drive['size_tb'],
                    sellMultiplier: $sellMultiplier,
                );
            }

            foreach ($poePlan as $index => $switch) {
                $lineItems[] = $this->lineItem(
                    key: "poe_switch_{$index}",
                    label: sprintf('PoE Switch %d-port', $switch['ports']),
                    quantity: $switch['quantity'],
                    unit: 'pcs',
                    unitCost: $poeSwitchUnitCost * ($switch['ports'] / 8),
                    sellMultiplier: $sellMultiplier,
                );
            }

            $lineItems[] = $this->lineItem(
                key: 'connectors',
                label: $connectorLabel,
                quantity: $connectorCount,
                unit: 'pcs',
                unitCost: $connectorUnitCost,
                sellMultiplier: $sellMultiplier,
            );
        }

        $lineItems[] = $this->lineItem(
            key: 'cable',
            label: 'Cable',
            quantity: $cableMeters,
            unit: 'm',
            unitCost: $cableUnitCost,
            sellMultiplier: $sellMultiplier,
        );

        $lineItems[] = $this->lineItem(
            key: 'trunking',
            label: 'Cable Trunking',
            quantity: $trunkingMeters,
            unit: 'm',
            unitCost: $trunkingUnitCost,
            sellMultiplier: $sellMultiplier,
        );

        $lineItems[] = $this->lineItem(
            key: 'installation',
            label: 'Installation & Configuration',
            quantity: $installationCost > 0 ? 1 : 0,
            unit: 'job',
            unitCost: $installationCost,
            sellMultiplier: $sellMultiplier,
        );

        foreach ($manualItems as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $lineItems[] = $this->lineItem(
                key: "manual_{$index}",
                label: trim((string) ($item['label'] ?? '')),
                quantity: $this->toFloat($item['quantity'] ?? 0),
                unit: filled($item['unit'] ?? null) ? (string) $item['unit'] : 'pcs',
                unitCost: $this->toFloat($item['unit_cost'] ?? 0),
                sellMultiplier: $sellMultiplier,
            );
        }

        $lineItems = array_values(array_filter(
            $lineItems,
            fn (array $item): bool => filled($item['label']) && $item['quantity'] > 0 && $item['unit_cost'] > 0,
        ));

        $costTotal = array_reduce(
            $lineItems,
            fn (float $carry, array $item): float => $carry + $item['cost_total'],
            0,
        );
        $finalTotal = array_reduce(
            $lineItems,
            fn (float $carry, array $item): float => $carry + $item['sell_total'],
            0,
        );
        $markupTotal = max(0, $finalTotal - $costTotal);

        return [
            'project_type' => $projectType,
            'camera_type' => $cameraType,
            'required_storage_tb' => $requiredStorageTb,
            'cost_total' => round($costTotal, 2),
            'markup_total' => round($markupTotal, 2),
            'final_total' => round($finalTotal, 2),
            'profit_total' => round($markupTotal, 2),
            'calculation' => [
                'line_items' => $lineItems,
                'requirements' => [
                    'recorder' => $usesCctvHardware && $cameraCount > 0
                        ? sprintf(
                            '1 x %d-channel %s',
                            $recorderChannels,
                            $cameraType === 'analog' ? 'DVR' : 'NVR',
                        )
                        : 'Not applicable',
                    'storage' => $usesCctvHardware
                        ? sprintf(
                            '%s TB (%s)',
                            $this->formatNumber($requiredStorageTb, 1),
                            $this->driveSummary($drivePlan),
                        )
                        : 'Not applicable',
                    'poe_switch' => $this->poeSummary($poePlan, $projectType),
                    'connectors' => $usesCctvHardware && $connectorCount > 0
                        ? sprintf('%d x %s', $connectorCount, $connectorLabel)
                        : 'Not applicable',
                ],
                'derived' => [
                    'recorder_channels' => $recorderChannels,
                    'drive_plan' => $drivePlan,
                    'poe_switch_plan' => $poePlan,
                    'connector_count' => $connectorCount,
                    'sell_multiplier' => $sellMultiplier,
                    'bitrate_per_camera_mbps' => $this->estimatedBitratePerCamera(
                        $cameraType,
                        $megapixels,
                        $framesPerSecond,
                    ),
                ],
                'manual_items_count' => count($manualItems),
            ],
        ];
    }

    private function requiredStorageTb(
        int $cameraCount,
        string $cameraType,
        float $megapixels,
        int $framesPerSecond,
        int $recordingHours,
        int $recordingDays,
        string $projectType,
    ): float {
        if ($projectType !== 'cctv' || $cameraCount <= 0) {
            return 0;
        }

        $bitratePerCamera = $this->estimatedBitratePerCamera(
            $cameraType,
            $megapixels,
            $framesPerSecond,
        );

        $storageTb = $bitratePerCamera
            * $cameraCount
            * max(1, $recordingHours)
            * max(1, $recordingDays)
            * 0.00045
            * 1.15;

        return round(max(0.5, $storageTb), 1);
    }

    private function estimatedBitratePerCamera(
        string $cameraType,
        float $megapixels,
        int $framesPerSecond,
    ): float {
        $base = match (true) {
            $megapixels <= 2 => 2.2,
            $megapixels <= 4 => 4.0,
            $megapixels <= 5 => 5.2,
            $megapixels <= 8 => 7.8,
            default => 10.5,
        };

        $frameFactor = max(0.7, $framesPerSecond / 15);
        $typeFactor = $cameraType === 'analog' ? 0.9 : 1.0;

        return round($base * $frameFactor * $typeFactor, 2);
    }

    private function recommendedRecorderChannels(int $cameraCount): int
    {
        foreach ([4, 8, 16, 32, 64, 128] as $channels) {
            if ($cameraCount <= $channels) {
                return $channels;
            }
        }

        return (int) (ceil($cameraCount / 32) * 32);
    }

    /**
     * @return array<int, array{size_tb:int, quantity:int}>
     */
    private function recommendedDrives(float $requiredStorageTb, string $projectType): array
    {
        if ($projectType !== 'cctv' || $requiredStorageTb <= 0) {
            return [];
        }

        $available = [20, 18, 16, 14, 12, 10, 8, 6, 4];
        $remaining = (int) ceil($requiredStorageTb);
        $drives = [];

        while ($remaining > 0) {
            $size = collect($available)->first(
                fn (int $drive): bool => $drive <= $remaining,
                4,
            );

            $drives[$size] = ($drives[$size] ?? 0) + 1;
            $remaining -= $size;
        }

        krsort($drives);

        return array_map(
            fn (int $size, int $quantity): array => [
                'size_tb' => $size,
                'quantity' => $quantity,
            ],
            array_keys($drives),
            array_values($drives),
        );
    }

    /**
     * @return array<int, array{ports:int, quantity:int}>
     */
    private function recommendedPoeSwitches(
        int $cameraCount,
        string $cameraType,
        string $projectType,
    ): array {
        if ($projectType !== 'cctv' || $cameraType !== 'ip' || $cameraCount <= 0) {
            return [];
        }

        $available = [48, 24, 16, 8];
        $remaining = $cameraCount;
        $switches = [];

        while ($remaining > 0) {
            $size = collect($available)->first(
                fn (int $ports): bool => $ports <= $remaining,
                8,
            );

            $switches[$size] = ($switches[$size] ?? 0) + 1;
            $remaining -= $size;
        }

        krsort($switches);

        return array_map(
            fn (int $ports, int $quantity): array => [
                'ports' => $ports,
                'quantity' => $quantity,
            ],
            array_keys($switches),
            array_values($switches),
        );
    }

    private function connectorCount(int $cameraCount, string $projectType): int
    {
        if ($projectType !== 'cctv' || $cameraCount <= 0) {
            return 0;
        }

        return $cameraCount * 2;
    }

    /**
     * @return array<string, mixed>
     */
    private function lineItem(
        string $key,
        string $label,
        float|int $quantity,
        string $unit,
        float $unitCost,
        float $sellMultiplier,
    ): array {
        $quantity = round((float) $quantity, 2);
        $sellUnit = round($unitCost * $sellMultiplier, 2);
        $costTotal = round($quantity * $unitCost, 2);
        $sellTotal = round($quantity * $sellUnit, 2);

        return [
            'key' => $key,
            'label' => $label,
            'quantity' => $quantity,
            'unit' => $unit,
            'unit_cost' => round($unitCost, 2),
            'sell_unit' => $sellUnit,
            'cost_total' => $costTotal,
            'sell_total' => $sellTotal,
        ];
    }

    /**
     * @param  array<int, array{size_tb:int, quantity:int}>  $drivePlan
     */
    private function driveSummary(array $drivePlan): string
    {
        if ($drivePlan === []) {
            return 'N/A';
        }

        return implode(', ', array_map(
            fn (array $drive): string => sprintf(
                '%d x %d TB',
                $drive['quantity'],
                $drive['size_tb'],
            ),
            $drivePlan,
        ));
    }

    /**
     * @param  array<int, array{ports:int, quantity:int}>  $poePlan
     */
    private function poeSummary(array $poePlan, string $projectType): string
    {
        if ($projectType !== 'cctv') {
            return 'Not applicable';
        }

        if ($poePlan === []) {
            return 'Not required';
        }

        return implode(', ', array_map(
            fn (array $switch): string => sprintf(
                '%d x %d-port',
                $switch['quantity'],
                $switch['ports'],
            ),
            $poePlan,
        ));
    }

    private function toFloat(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) round((float) $value) : 0;
    }

    private function formatNumber(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals, '.', ',');
    }
}
