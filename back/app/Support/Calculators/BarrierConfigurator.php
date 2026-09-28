<?php

namespace App\Support\Calculators;

final class BarrierConfigurator
{
    /** @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    public function normalize(array $config, ?string $changedKey = null): array
    {
        $barriers = BarrierDeviceCatalog::barriers();
        $lprs = BarrierDeviceCatalog::lprCameras();
        $uhf = BarrierDeviceCatalog::uhfReaders();

        $length = (float) ($config['boom_length'] ?? 4.5);
        $type = (string) ($config['boom_type'] ?? 'straight');
        $accessMode = (string) ($config['access_mode'] ?? 'lpr');

        $barrierId = (string) ($config['barrier_id'] ?? '');
        $barrier = $barriers[$barrierId] ?? null;
        if (! $this->barrierWorks($barrier, $length, $type)) {
            $barrierId = $this->bestBarrier($length, $type) ?? array_key_first($barriers);
            $config['barrier_id'] = $barrierId;
            $barrier = $barriers[$barrierId];

            if (! in_array($length, (array) ($barrier['boom_lengths'] ?? []), true)) {
                $config['boom_length'] = (float) (($barrier['boom_lengths'][0] ?? 3));
            }
        }

        if ($accessMode === 'lpr') {
            $lprId = (string) ($config['lpr_camera_id'] ?? '');
            $camera = $lprs[$lprId] ?? null;

            if (! $camera || ! ($camera['relay'] ?? false) || ! ($camera['network'] ?? false)) {
                $config['lpr_camera_id'] = 'hikvision-tcg406-e';
            }

            $config['vehicle_trigger'] = $this->normalizeTrigger(
                (string) ($config['vehicle_trigger'] ?? 'video'),
                $lprs[$config['lpr_camera_id']] ?? null,
            );
        }

        if ($accessMode === 'uhf') {
            $uhfId = (string) ($config['uhf_reader_id'] ?? '');
            if (! isset($uhf[$uhfId])) {
                $config['uhf_reader_id'] = 'zkteco-uhf5-pro';
            }

            $config['controller_interface'] = in_array(
                (string) ($config['controller_interface'] ?? 'wiegand'),
                (array) $uhf[$config['uhf_reader_id']]['interfaces'],
                true,
            ) ? (string) $config['controller_interface'] : 'wiegand';
        }

        if ($accessMode === 'remote') {
            $config['vehicle_trigger'] = 'none';
        }

        return $config;
    }

    /** @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    public function configure(array $config): array
    {
        $c = $this->normalize($config);
        $barrier = BarrierDeviceCatalog::barriers()[$c['barrier_id']];
        $lanes = max(1, min(8, (int) ($c['lanes'] ?? 1)));
        $mode = (string) ($c['access_mode'] ?? 'lpr');

        $items = [
            ['group' => 'შლაგბაუმი', 'qty' => $lanes, 'item' => BarrierDeviceCatalog::label($barrier), 'why' => 'არჩეული boom length/type თავსებადია ამ მოდელთან.'],
            ['group' => 'Loop detector', 'qty' => $lanes, 'item' => 'Vehicle loop detector + inductive loop', 'why' => 'მანქანის ყოფნის კონტროლი და დახურვის უსაფრთხო ლოგიკა.'],
            ['group' => 'Photo beam', 'qty' => $lanes, 'item' => 'IR safety photocell pair', 'why' => 'დამატებითი anti-crush უსაფრთხოების ფენა.'],
        ];

        $selection = ['barrier' => BarrierDeviceCatalog::label($barrier)];

        if ($mode === 'lpr') {
            $camera = BarrierDeviceCatalog::lprCameras()[$c['lpr_camera_id']];
            $items[] = ['group' => 'LPR/ANPR კამერა', 'qty' => $lanes, 'item' => BarrierDeviceCatalog::label($camera), 'why' => 'აქვს ქსელი და relay output; არჩეული trigger რეჟიმიც მხარდაჭერილია.'];
            $items[] = ['group' => 'ქსელი', 'qty' => 1, 'item' => 'PoE/network switch + LAN uplink', 'why' => 'LPR კამერების, მართვის სისტემისა და დისტანციური წვდომისთვის.'];
            $selection['lpr_camera'] = BarrierDeviceCatalog::label($camera);
            $selection['trigger'] = strtoupper((string) $c['vehicle_trigger']);
        } elseif ($mode === 'uhf') {
            $reader = BarrierDeviceCatalog::uhfReaders()[$c['uhf_reader_id']];
            $items[] = ['group' => 'UHF Reader', 'qty' => $lanes, 'item' => BarrierDeviceCatalog::label($reader), 'why' => 'არჩეული Wiegand/RS485 ინტერფეისი მხარდაჭერილია.'];
            $items[] = ['group' => 'UHF Tag', 'qty' => max(1, (int) ($c['tag_count'] ?? 20)), 'item' => 'UHF vehicle tags', 'why' => 'თითო ავტოტრანსპორტზე ცალკე tag.'];
            $selection['uhf_reader'] = BarrierDeviceCatalog::label($reader);
            $selection['interface'] = strtoupper((string) $c['controller_interface']);
        } else {
            $items[] = ['group' => 'მართვა', 'qty' => $lanes, 'item' => 'Remote / wall button / relay input', 'why' => 'ხელით ან გარე კონტროლერის relay-ით გახსნა.'];
        }

        $items[] = ['group' => 'სარეზერვო კვება', 'qty' => 1, 'item' => 'UPS / backup power sizing by actual barrier + network load', 'why' => 'ზუსტი UPS შეირჩევა მოწყობილობების რეალური W და სასურველი ავტონომიის მიხედვით.'];

        $warnings = [];
        if ((float) $c['boom_length'] >= 6) {
            $warnings[] = '6 მ boom-ზე გამოიყენეთ მხოლოდ მოდელი/კონფიგურაცია, რომელსაც მწარმოებელი უშვებს ამ სიგრძეზე; სიჩქარე ჩვეულებრივ უფრო ნელია.';
        }
        if ($mode === 'lpr') {
            $warnings[] = 'LPR მუშაობისთვის კამერის კუთხე, დაშორება, სიჩქარე, განათება და ქვეყნის/ნომრის ფორმატის მხარდაჭერა ადგილზე გადაამოწმეთ.';
        }

        return [
            'summary' => "{$lanes} ზოლის შლაგბაუმის თავსებადი წინასწარი კომპლექტაცია",
            'selection' => $selection,
            'items' => $items,
            'checks' => [
                'Boom length/type ↔ Barrier model — თავსებადობა შემოწმებულია კატალოგის მონაცემებით.',
                $mode === 'lpr'
                    ? 'LPR camera ↔ Trigger ↔ Barrier relay — არჩეული camera profile-ში თავსებადია.'
                    : ($mode === 'uhf'
                        ? 'UHF reader ↔ Controller interface — არჩეული ინტერფეისი მხარდაჭერილია.'
                        : 'Manual/relay control — barrier control input ადგილზე გადაამოწმეთ.'),
                'Loop detector + photocell — რეკომენდებულია დახურვის უსაფრთხოების ლოგიკისთვის.',
            ],
            'warnings' => $warnings,
        ];
    }

    /** @param array<string, mixed>|null $barrier */
    private function barrierWorks(?array $barrier, float $length, string $type): bool
    {
        if (! $barrier || ! in_array($type, (array) ($barrier['boom_types'] ?? []), true)) {
            return false;
        }

        foreach ((array) ($barrier['boom_lengths'] ?? []) as $supported) {
            if (abs((float) $supported - $length) < 0.01) {
                return true;
            }
        }

        return false;
    }

    private function bestBarrier(float $length, string $type): ?string
    {
        foreach (BarrierDeviceCatalog::barriers() as $id => $barrier) {
            if ($this->barrierWorks($barrier, $length, $type)) {
                return $id;
            }
        }

        return null;
    }

    /** @param array<string, mixed>|null $camera */
    private function normalizeTrigger(string $trigger, ?array $camera): string
    {
        if ($camera && in_array($trigger, (array) ($camera['trigger'] ?? []), true)) {
            return $trigger;
        }

        return (string) (($camera['trigger'][0] ?? 'video'));
    }
}
