<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $setting = DB::table('site_settings')->where('key', 'translations')->first();

        if (! $setting) {
            return;
        }

        $value = is_string($setting->value)
            ? json_decode($setting->value, true)
            : (array) $setting->value;
        $entries = is_array($value['entries'] ?? null) ? $value['entries'] : [];

        $replacements = [
            'home.hero.eyebrow' => [
                'old' => [
                    'ka' => 'IT და უსაფრთხოების სისტემები საქართველოში',
                    'en' => 'IT and security systems in Georgia',
                    'ru' => 'IT и системы безопасности в Грузии',
                ],
                'new' => [
                    'ka' => 'ვიდეოკამერები • ქსელები და Wi‑Fi • დაშვების კონტროლი • შლაგბაუმები • POS • IT Support',
                    'en' => 'CCTV • Networks & Wi-Fi • Access control • Barriers • POS • IT Support',
                    'ru' => 'Видеонаблюдение • Сети и Wi-Fi • Контроль доступа • Шлагбаумы • POS • IT Support',
                ],
            ],
            'home.hero.titlePrefix' => [
                'old' => [
                    'ka' => 'ტექნოლოგიური ინფრასტრუქტურა',
                    'en' => 'Technology infrastructure',
                    'ru' => 'Технологическая инфраструктура',
                ],
                'new' => [
                    'ka' => 'IT მომსახურება და უსაფრთხოების სისტემების მონტაჟი',
                    'en' => 'IT services and security systems installation',
                    'ru' => 'IT-услуги и монтаж систем безопасности',
                ],
            ],
            'home.hero.titleAccent' => [
                'old' => [
                    'ka' => 'თქვენი ობიექტისთვის',
                    'en' => 'built for your property',
                    'ru' => 'для вашего объекта',
                ],
                'new' => [
                    'ka' => 'თბილისში და საქართველოში',
                    'en' => 'in Tbilisi and across Georgia',
                    'ru' => 'в Тбилиси и по всей Грузии',
                ],
            ],
            'home.hero.description' => [
                'old' => [
                    'ka' => 'ვგეგმავთ, ვამონტაჟებთ და ვმართავთ კამერებს, დაშვების სისტემებს, ქსელებს, POS სისტემებსა და კომპიუტერულ ინფრასტრუქტურას.',
                    'en' => 'We design, install, and support CCTV, access control, networks, POS systems, and computer infrastructure.',
                    'ru' => 'Проектируем, устанавливаем и обслуживаем камеры, системы контроля доступа, сети, POS и компьютерную инфраструктуру.',
                ],
                'new' => [
                    'ka' => 'ვგეგმავთ, ვამონტაჟებთ და ვმართავთ ვიდეოსამეთვალყურეობას, LAN/Wi‑Fi ქსელებს, დაშვების კონტროლს, შლაგბაუმებს, POS სისტემებსა და IT ინფრასტრუქტურას — კერძო და ბიზნეს ობიექტებისთვის.',
                    'en' => 'We design, install, and support CCTV, LAN/Wi-Fi networks, access control, barrier gates, POS systems, and IT infrastructure for homes and businesses.',
                    'ru' => 'Проектируем, устанавливаем и обслуживаем видеонаблюдение, LAN/Wi-Fi сети, контроль доступа, шлагбаумы, POS и IT-инфраструктуру для частных и коммерческих объектов.',
                ],
            ],
        ];

        $changed = false;

        foreach ($entries as &$entry) {
            $key = (string) ($entry['key'] ?? '');

            if (! isset($replacements[$key])) {
                continue;
            }

            foreach (['ka', 'en', 'ru'] as $locale) {
                $current = trim((string) ($entry[$locale] ?? ''));
                $old = $replacements[$key]['old'][$locale];

                // Preserve editorial changes made in the CMS. Only migrate the
                // previous canonical copy or fill a blank canonical field.
                if ($current === '' || $current === $old) {
                    $entry[$locale] = $replacements[$key]['new'][$locale];
                    $changed = true;
                }
            }
        }
        unset($entry);

        if (! $changed) {
            return;
        }

        $value['entries'] = $entries;

        DB::table('site_settings')
            ->where('key', 'translations')
            ->update([
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Editorial content migrations are intentionally not destructive.
    }
};
