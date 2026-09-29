<?php

namespace App\Support\Calculators;

final class AccessIntercomConfigurator
{
    /** @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    public function normalize(array $config, ?string $changedKey = null): array
    {
        if (($config['system'] ?? 'access') === 'access') {
            return $this->normalizeAccess($config, $changedKey);
        }

        return $this->normalizeIntercom($config, $changedKey);
    }

    /** @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function configure(array $input): array
    {
        $input = $this->normalize($input);
        $system = (string) ($input['system'] ?? 'access');

        return $system === 'access'
            ? $this->accessResult($input)
            : $this->intercomResult($input);
    }

    /** @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function normalizeAccess(array $config, ?string $changedKey): array
    {
        $controllers = AccessIntercomDeviceCatalog::controllers();
        $readers = AccessIntercomDeviceCatalog::readers();

        $doors = max(1, min(16, (int) ($config['doors'] ?? 1)));
        $sides = ($config['reader_sides'] ?? 'entry') === 'entry_exit' ? 2 : 1;
        $credential = (string) ($config['credential'] ?? 'mifare');
        $interface = (string) ($config['reader_interface'] ?? 'wiegand');

        $readerId = (string) ($config['reader_id'] ?? '');
        $reader = $readers[$readerId] ?? null;

        if ($changedKey === 'reader_id' && $reader) {
            $credential = (string) $reader['credential'];
            $common = array_values(array_intersect((array) $reader['interfaces'], ['osdp', 'wiegand', 'rs485']));
            $interface = in_array($interface, $common, true) ? $interface : ($common[0] ?? 'wiegand');
            $config['credential'] = $credential;
            $config['reader_interface'] = $interface;
        }

        if (! $reader
            || ($reader['credential'] ?? null) !== $credential
            || ! in_array($interface, (array) ($reader['interfaces'] ?? []), true)) {
            $readerId = $this->bestReader($credential, $interface) ?? $this->bestReader($credential, null) ?? array_key_first($readers);
            $reader = $readers[$readerId];
            $config['reader_id'] = $readerId;

            if (! in_array($interface, (array) $reader['interfaces'], true)) {
                $interface = (string) ($reader['interfaces'][0] ?? 'wiegand');
                $config['reader_interface'] = $interface;
            }
        }

        $controllerId = (string) ($config['controller_id'] ?? '');
        $controller = $controllers[$controllerId] ?? null;

        if (! $this->controllerWorks($controller, $doors, $sides, $interface)) {
            $controllerId = $this->bestController($doors, $sides, $interface)
                ?? $this->bestController($doors, $sides, 'wiegand')
                ?? array_key_first($controllers);
            $config['controller_id'] = $controllerId;
            $controller = $controllers[$controllerId];

            if (! in_array($interface, (array) $controller['interfaces'], true)) {
                $interface = (string) ($controller['interfaces'][0] ?? 'wiegand');
                $config['reader_interface'] = $interface;
                $readerId = $this->bestReader($credential, $interface) ?? $readerId;
                $config['reader_id'] = $readerId;
            }
        }

        return $config;
    }

    /** @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function normalizeIntercom(array $config, ?string $changedKey): array
    {
        return (new IntercomPlanner)->normalize($config);
    }

    /** @param array<string, mixed> $c */
    private function accessResult(array $c): array
    {
        $controllers = AccessIntercomDeviceCatalog::controllers();
        $readers = AccessIntercomDeviceCatalog::readers();
        $controller = $controllers[$c['controller_id']];
        $reader = $readers[$c['reader_id']];

        $doors = max(1, (int) $c['doors']);
        $sides = ($c['reader_sides'] ?? 'entry') === 'entry_exit' ? 2 : 1;
        $readersCount = $doors * $sides;
        $interface = (string) $c['reader_interface'];

        $controllerCount = max(
            (int) ceil($doors / max(1, (int) $controller['doors'])),
            (int) ceil($readersCount / max(1, (int) ($controller['reader_ports'][$interface] ?? 0))),
        );

        $lockCurrent = max(0.1, (float) ($c['lock_current_a'] ?? 0.5));
        $controllerCurrent = max(0.1, (float) ($c['controller_current_a'] ?? 0.3));
        $readerCurrent = max(0.05, (float) ($c['reader_current_a'] ?? 0.12));
        $reserve = max(0, min(100, (float) ($c['reserve_percent'] ?? 30)));
        $loadA = ($doors * $lockCurrent) + ($readersCount * $readerCurrent) + ($controllerCount * $controllerCurrent);
        $recommendedA = ceil(($loadA * (1 + $reserve / 100)) * 10) / 10;

        return [
            'system' => 'access',
            'summary' => "{$doors} კარის RFID / დაშვების სისტემის თავსებადი კომპლექტაცია",
            'compatible' => true,
            'selection' => [
                'controller' => AccessIntercomDeviceCatalog::label($controller),
                'reader' => AccessIntercomDeviceCatalog::label($reader),
                'interface' => strtoupper($interface),
                'credential' => ($c['credential'] ?? 'mifare') === 'em' ? 'EM 125 kHz' : 'MIFARE 13.56 MHz',
            ],
            'items' => [
                ['group' => 'კონტროლერი', 'qty' => $controllerCount, 'item' => AccessIntercomDeviceCatalog::label($controller), 'why' => 'კარის არხები და reader port-ები საკმარისია არჩეული ტოპოლოგიისთვის.'],
                ['group' => 'RFID Reader', 'qty' => $readersCount, 'item' => AccessIntercomDeviceCatalog::label($reader), 'why' => strtoupper($interface).' + '.(($c['credential'] ?? 'mifare') === 'em' ? 'EM 125 kHz' : 'MIFARE 13.56 MHz').' თავსებადია.'],
                ['group' => 'საკეტი', 'qty' => $doors, 'item' => $this->lockLabel((string) ($c['lock_type'] ?? 'maglock')), 'why' => 'თითო კარზე დამოუკიდებელი lock relay / კვება.'],
                ['group' => 'Exit ღილაკი', 'qty' => $sides === 1 ? $doors : 0, 'item' => 'NO/NC exit button', 'why' => 'საჭიროა, როცა გამოსვლა reader-ით არ იმართება.'],
                ['group' => 'Door contact', 'qty' => $doors, 'item' => 'მაგნიტური კარის სენსორი', 'why' => 'კარის სტატუსისა და forced/held-open კონტროლისთვის.'],
                ['group' => 'კვება', 'qty' => 1, 'item' => "12V DC PSU მინ. {$recommendedA}A + battery backup", 'why' => 'დათვლილი დატვირთვა ≈ '.number_format($loadA, 1)."A + {$reserve}% რეზერვი."],
            ],
            'checks' => [
                'Controller ↔ Reader: '.strtoupper($interface).' მხარდაჭერა ორივე მხარეს დადასტურებულია კატალოგის მონაცემებით.',
                'Card ↔ Reader: არჩეული RFID ტექნოლოგია reader-ის ტექნოლოგიას ემთხვევა.',
                'Controller ↔ Lock: გადაამოწმეთ NO/NC relay logic, contact rating და fire/emergency release მოთხოვნა.',
            ],
            'warnings' => ($c['lock_type'] ?? 'maglock') === 'maglock'
                ? ['Maglock ჩვეულებრივ fail-safe სისტემაა; ავარიული გახსნა და სახანძრო ინტეგრაცია ცალკე გადაამოწმეთ.']
                : [],
            'electrical' => [
                'doors' => $doors, 'readers' => $readersCount,
                'estimated_load_a' => round($loadA, 1), 'recommended_psu_a' => $recommendedA,
            ],
        ];
    }

    /** @param array<string, mixed> $c */
    private function intercomResult(array $c): array
    {
        return (new IntercomPlanner)->result($c);
    }

    /** @param array<string, mixed>|null $controller */
    private function controllerWorks(?array $controller, int $doors, int $sides, string $interface): bool
    {
        if (! $controller || ! in_array($interface, (array) ($controller['interfaces'] ?? []), true)) {
            return false;
        }

        $controllerDoors = max(1, (int) ($controller['doors'] ?? 1));
        $ports = (int) ($controller['reader_ports'][$interface] ?? 0);

        return $ports >= ($controllerDoors * $sides);
    }

    private function bestController(int $doors, int $sides, string $interface): ?string
    {
        $candidates = [];
        foreach (AccessIntercomDeviceCatalog::controllers() as $id => $controller) {
            if (! $this->controllerWorks($controller, $doors, $sides, $interface)) {
                continue;
            }

            $count = (int) ceil($doors / max(1, (int) $controller['doors']));
            $candidates[$id] = [$count, -(int) $controller['doors']];
        }

        uasort($candidates, fn (array $a, array $b): int => $a <=> $b);

        return array_key_first($candidates);
    }

    private function bestReader(string $credential, ?string $interface): ?string
    {
        foreach (AccessIntercomDeviceCatalog::readers() as $id => $reader) {
            if (($reader['credential'] ?? null) !== $credential) {
                continue;
            }
            if ($interface !== null && ! in_array($interface, (array) $reader['interfaces'], true)) {
                continue;
            }

            return $id;
        }

        return null;
    }

    private function lockLabel(string $lockType): string
    {
        return match ($lockType) {
            'strike' => 'Electric strike (fail-secure/fail-safe პროექტის მიხედვით)',
            'bolt' => 'Electric bolt lock',
            default => 'Electromagnetic lock (maglock, fail-safe)',
        };
    }
}
