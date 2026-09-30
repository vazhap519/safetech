<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, string> */
    private const SERVICE_ALIASES = [
        'ip-camera-installation' => 'security-camera-installation',
        'video-surveillance-system-installation' => 'security-camera-installation',
        'intercom-installation' => 'intercom-access-control-installation',
        'access-control-system-installation' => 'access-control-installation',
        'barrier-gate-setup' => 'barrier-gate-installation',
        'personal-computer-assembly' => 'custom-computer-build',
        'computer-upgrade-optimization' => 'computer-setup-optimization',
        'computer-component-replacement' => 'computer-component-upgrades',
        'computer-preventive-maintenance' => 'computer-cleaning-maintenance',
        'computer-peripheral-troubleshooting' => 'computer-peripheral-setup',
        'cat6-cabling' => 'network-cable-installation',
        'lan-installation' => 'lan-network-installation',
        'wifi-network-installation' => 'router-wifi-configuration',
        'network-rack-installation' => 'rack-assembly-cable-management',
        'patch-panel-installation' => 'patch-panel-network-outlet-installation',
        'it-technical-support' => 'business-it-support',
        'computers-workstations-setup' => 'workstation-setup',
        'microsoft-365-setup-migration' => 'microsoft-365-migration',
        'computer-network-diagnostics' => 'network-diagnostics',
        'data-backup-recovery' => 'backup-setup',
        'macos-installation-configuration' => 'macos-installation',
        'macbook-imac-software-setup' => 'mac-software-setup',
        'mac-software-installation' => 'mac-app-installation',
        'mac-diagnostics-repair' => 'mac-diagnostics',
        'mac-data-recovery-backup' => 'mac-backup-migration',
        'structured-cabling-installation' => 'structured-cabling',
        'telecommunications-infrastructure-installation' => 'communications-infrastructure',
    ];

    public function up(): void
    {
        $this->configureServiceAreaBusiness();
        $this->consolidateDuplicateServices();
        $this->refineComputerServicesCategory();
    }

    public function down(): void
    {
        // These are SEO/editorial corrections. Do not restore stale addresses
        // or republish duplicate search-intent pages on rollback.
    }

    private function configureServiceAreaBusiness(): void
    {
        $setting = DB::table('site_settings')->where('key', 'contact')->first();

        if (! $setting) {
            return;
        }

        $value = $this->decode($setting->value ?? null);
        $value['service_area_business'] = true;
        $value['address'] = '';
        $value['address_en'] = '';
        $value['address_ru'] = '';

        DB::table('site_settings')->where('id', $setting->id)->update([
            'value' => $this->json($value),
            'updated_at' => now(),
        ]);
    }

    private function consolidateDuplicateServices(): void
    {
        $services = DB::table('services')
            ->whereIn('slug', array_keys(self::SERVICE_ALIASES))
            ->get(['id', 'seo']);

        foreach ($services as $service) {
            $seo = $this->decode($service->seo ?? null);
            $seo['noindex'] = true;

            DB::table('services')->where('id', $service->id)->update([
                'seo' => $this->json($seo),
                'is_published' => false,
                'updated_at' => now(),
            ]);

            DB::table('local_service_landings')
                ->where('service_id', $service->id)
                ->update([
                    'noindex' => true,
                    'is_published' => false,
                    'updated_at' => now(),
                ]);
        }
    }

    private function refineComputerServicesCategory(): void
    {
        $category = DB::table('category_for_services')
            ->where('slug', 'computer-services')
            ->first();

        if (! $category) {
            return;
        }

        $titles = [
            'ka' => 'კომპიუტერის გამართვა, აწყობა და პროგრამული მომსახურება',
            'en' => 'Computer Setup, Assembly & Software Services',
            'ru' => 'Настройка, сборка и программное обслуживание компьютеров',
        ];
        $descriptions = [
            'ka' => 'კომპიუტერის აწყობა, Windows-ისა და პროგრამების გამართვა, კომპონენტების განახლება, პერიფერიის დაკავშირება და პროფილაქტიკური მომსახურება. აპარატურულ შეკეთებას SafeTech არ სთავაზობს.',
            'en' => 'PC assembly, Windows and software setup, component upgrades, peripheral configuration and preventive maintenance. SafeTech does not offer component-level hardware repair.',
            'ru' => 'Сборка ПК, настройка Windows и программ, модернизация компонентов, подключение периферии и профилактическое обслуживание. SafeTech не выполняет компонентный аппаратный ремонт.',
        ];
        $translations = $this->decode($category->translations ?? null);
        $translations['fields'] ??= [];
        $translations['fields']['seo_title'] = $titles;
        $translations['fields']['seo_description'] = $descriptions;
        $translations['fields']['intro_text'] = $descriptions;

        DB::table('category_for_services')->where('id', $category->id)->update([
            'seo_title' => $titles['ka'],
            'seo_description' => $descriptions['ka'],
            'intro_text' => $descriptions['ka'],
            'translations' => $this->json($translations),
            'updated_at' => now(),
        ]);
    }

    /** @return array<string, mixed> */
    private function decode(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function json(array $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: '{}';
    }
};
