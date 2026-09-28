<?php

namespace App\Support\Calculators;

final class AccessIntercomConfigurator
{
    /** @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function configure(array $input): array
    {
        $system = (string) ($input['system'] ?? 'access');
        $doors = max(1, min(16, (int) ($input['doors'] ?? 1)));
        $readerSides = ($input['reader_sides'] ?? 'entry') === 'entry_exit' ? 2 : 1;
        $readerInterface = (string) ($input['reader_interface'] ?? 'wiegand');
        $credential = (string) ($input['credential'] ?? 'mifare');
        $lockType = (string) ($input['lock_type'] ?? 'maglock');
        $lockCurrent = max(0.1, (float) ($input['lock_current_a'] ?? 0.5));
        $controllerCurrent = max(0.1, (float) ($input['controller_current_a'] ?? 0.3));
        $readerCurrent = max(0.05, (float) ($input['reader_current_a'] ?? 0.12));
        $reserve = max(0, min(100, (float) ($input['reserve_percent'] ?? 30)));

        $readers = $doors * $readerSides;
        $controllerDoors = $this->controllerDoorCapacity($doors);
        $controllerCount = (int) ceil($doors / $controllerDoors);
        $loadA = ($doors * $lockCurrent) + ($readers * $readerCurrent) + ($controllerCount * $controllerCurrent);
        $recommendedA = ceil(($loadA * (1 + ($reserve / 100))) * 10) / 10;

        $items = [];
        $warnings = [];
        $checks = [];

        if ($system === 'access') {
            $items[] = [
                'group' => 'კონტროლერი',
                'qty' => $controllerCount,
                'item' => $this->controllerLabel($controllerDoors, $readerInterface),
                'why' => "{$doors} კარისთვის საჭიროა მინიმუმ {$doors} მართვადი რელე/კარის არხი.",
            ];
            $items[] = [
                'group' => 'RFID Reader',
                'qty' => $readers,
                'item' => $this->readerLabel($credential, $readerInterface),
                'why' => $readerSides === 2 ? 'Reader საჭიროა შესასვლელზეც და გამოსასვლელზეც.' : 'Reader საჭიროა შესასვლელ მხარეს.',
            ];
            $items[] = [
                'group' => 'საკეტი',
                'qty' => $doors,
                'item' => $this->lockLabel($lockType),
                'why' => 'თითო კარს სჭირდება დამოუკიდებელი საკეტი და შესაბამისი კვება.',
            ];
            $items[] = [
                'group' => 'Exit ღილაკი',
                'qty' => $readerSides === 1 ? $doors : 0,
                'item' => $readerSides === 1 ? 'NO/NC exit button' : 'არ არის აუცილებელი, თუ გამოსასვლელზეც reader გამოიყენება',
                'why' => 'გამოსვლის ლოგიკა უნდა დაემთხვეს reader/ღილაკის სქემას.',
            ];
            $items[] = [
                'group' => 'Door contact',
                'qty' => $doors,
                'item' => 'მაგნიტური კარის სენსორი',
                'why' => 'კარის სტატუსი, forced-open და held-open მონიტორინგისთვის.',
            ];
            $items[] = [
                'group' => 'კვება',
                'qty' => 1,
                'item' => "12V DC PSU მინ. {$recommendedA}A + ბატარეის მხარდაჭერა",
                'why' => "დათვლილი დატვირთვა ≈ ".number_format($loadA, 1)."A; დამატებულია {$reserve}% რეზერვი.",
            ];

            $checks[] = $readerInterface === 'osdp'
                ? 'კონტროლერს და reader-ს ორივეს უნდა ჰქონდეს OSDP/RS-485 მხარდაჭერა და თავსებადი secure channel პარამეტრები.'
                : 'კონტროლერის reader input და reader output ორივე უნდა იყოს Wiegand (მაგ. W26/W34 მხარდაჭერა გადაამოწმეთ).';
            $checks[] = $credential === 'mifare'
                ? 'ბარათი/ბრელოკი და reader უნდა იყოს 13.56 MHz MIFARE-compatible.'
                : 'ბარათი/ბრელოკი და reader უნდა იყოს 125 kHz EM-compatible.';
            $checks[] = 'საკეტის NO/NC ლოგიკა უნდა დაემთხვეს კონტროლერის relay output-ს და fire/emergency მოთხოვნებს.';

            if ($lockType === 'maglock') {
                $warnings[] = 'Maglock ჩვეულებრივ fail-safe ტიპია: კვების დაკარგვისას იღება. ავარიული გახსნა და სახანძრო ინტეგრაცია ცალკე გადაამოწმეთ.';
            }
            if ($readerInterface === 'wiegand') {
                $warnings[] = 'Wiegand მარტივია, მაგრამ OSDP-სთან შედარებით ნაკლებად დაცულია. ახალ ობიექტზე OSDP სასურველია, თუ ორივე მხარე უჭერს მხარს.';
            }
        } else {
            $intercomType = (string) ($input['intercom_type'] ?? 'ip');
            $apartments = max(1, min(200, (int) ($input['apartments'] ?? 1)));
            $monitorsPerApartment = max(1, min(4, (int) ($input['monitors_per_apartment'] ?? 1)));
            $monitors = $apartments * $monitorsPerApartment;

            $items[] = [
                'group' => 'გარე პანელი',
                'qty' => 1,
                'item' => $intercomType === 'ip' ? 'IP ვიდეოდომოფონის გარე პანელი, relay output-ით' : '2-wire ვიდეოდომოფონის გარე პანელი',
                'why' => 'გარე პანელი და შიდა მონიტორები უნდა იყოს ერთი თავსებადი პლატფორმის/ოჯახის.',
            ];
            $items[] = [
                'group' => 'შიდა მონიტორი',
                'qty' => $monitors,
                'item' => $intercomType === 'ip' ? 'IP indoor monitor' : '2-wire indoor monitor',
                'why' => "{$apartments} ბინა × {$monitorsPerApartment} მონიტორი.",
            ];
            $items[] = [
                'group' => $intercomType === 'ip' ? 'ქსელი / PoE' : '2-wire distributor',
                'qty' => $intercomType === 'ip' ? (int) ceil(($monitors + 1) / 8) : (int) ceil($apartments / 4),
                'item' => $intercomType === 'ip' ? 'PoE switch შესაბამისი PoE budget-ით' : 'მწარმოებლის თავსებადი 2-wire distributor / power module',
                'why' => $intercomType === 'ip' ? 'ყველა IP მოწყობილობის პორტები და PoE budget წინასწარ დაითვალეთ.' : '2-wire სისტემაში distributor/power module უნდა ეკუთვნოდეს იმავე ეკოსისტემას.',
            ];
            $items[] = [
                'group' => 'ელ. საკეტი',
                'qty' => 1,
                'item' => $this->lockLabel($lockType),
                'why' => 'გარე პანელის relay ან ცალკე access controller მართავს საკეტს.',
            ];

            $checks[] = $intercomType === 'ip'
                ? 'Outdoor station, indoor monitor და management software უნდა იყოს ერთი თავსებადი IP intercom ecosystem-ის.'
                : '2-wire outdoor station, monitors, distributor და power module არ აურიოთ სხვა 2-wire სტანდარტთან მხოლოდ კონექტორის მსგავსების გამო.';
            $checks[] = 'გარე პანელის relay contact rating და lock PSU ცალ-ცალკე გადაამოწმეთ; საკეტის დენი პირდაპირ პანელიდან არ გაატაროთ, თუ datasheet ამას არ ითვალისწინებს.';
            $checks[] = 'თუ RFID reader გარე პანელშია ჩაშენებული, credential technology (MIFARE/EM) უნდა დაემთხვეს გამოყენებულ ბარათებს.';

            if ($intercomType === 'ip') {
                $warnings[] = 'PoE სტანდარტი (802.3af/at ან passive) ზუსტად უნდა ემთხვეოდეს მოწყობილობას; passive PoE ავტომატურად თავსებადი არ არის.';
            }
        }

        return [
            'system' => $system,
            'summary' => $system === 'access'
                ? "{$doors} კარის RFID/დაშვების სისტემის წინასწარი კომპლექტაცია"
                : 'ვიდეოდომოფონის სისტემის წინასწარი კომპლექტაცია',
            'items' => array_values(array_filter($items, fn (array $item): bool => ($item['qty'] ?? 0) !== 0)),
            'checks' => $checks,
            'warnings' => $warnings,
            'electrical' => $system === 'access' ? [
                'doors' => $doors,
                'readers' => $readers,
                'estimated_load_a' => round($loadA, 1),
                'recommended_psu_a' => $recommendedA,
            ] : null,
        ];
    }

    private function controllerDoorCapacity(int $doors): int
    {
        return $doors <= 1 ? 1 : ($doors <= 2 ? 2 : 4);
    }

    private function controllerLabel(int $doors, string $interface): string
    {
        $example = match ($doors) {
            1 => '1-door network controller (მაგ. ZKTeco C3-100 class)',
            2 => '2-door network controller (მაგ. ZKTeco C3-200 class)',
            default => '4-door network controller (მაგ. ZKTeco C3-400 class)',
        };

        return $example.' / reader interface: '.strtoupper($interface);
    }

    private function readerLabel(string $credential, string $interface): string
    {
        $card = $credential === 'em' ? '125 kHz EM' : '13.56 MHz MIFARE';

        return "{$card} reader / ".strtoupper($interface);
    }

    private function lockLabel(string $lockType): string
    {
        return match ($lockType) {
            'strike' => 'Electric strike (fail-secure/fail-safe ვარიანტი პროექტის მიხედვით)',
            'bolt' => 'Electric bolt lock',
            default => 'Electromagnetic lock (maglock, fail-safe)',
        };
    }
}
