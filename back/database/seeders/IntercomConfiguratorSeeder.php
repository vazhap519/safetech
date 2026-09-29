<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Support\Calculators\IntercomProfile;
use Illuminate\Database\Seeder;

final class IntercomConfiguratorSeeder extends Seeder
{
    public function run(): void
    {
        Service::query()->each(function (Service $service): void {
            if (! IntercomProfile::matches((string) $service->slug, (string) $service->name)) {
                return;
            }
            $current = is_array($service->lead_form) ? $service->lead_form : [];
            if (($current['intercom_version'] ?? 0) >= IntercomProfile::VERSION) {
                return;
            }
            $defaults = IntercomProfile::profile();
            $fields = collect($current['extra_fields'] ?? [])->keyBy('key');
            $components = collect($current['components'] ?? [])->keyBy('key');
            $config = array_replace($defaults, $current);
            // Retain the previous configuration for review, including legacy generic prices.
            $config['intercom_previous_config'] = $current;
            $config['intercom_version'] = IntercomProfile::VERSION;
            $config['project_size_options'] = $defaults['project_size_options'];
            foreach (['ka', 'en', 'ru'] as $locale) {
                $config['project_size_label_'.$locale] = $defaults['project_size_label_'.$locale];
                $config['calculator_disclaimer_'.$locale] = $defaults['calculator_disclaimer_'.$locale];
            }
            $config['extra_fields'] = array_map(function (array $field) use ($fields): array {
                $old = $fields->pull($field['key'], []);

                // Preserve explicit field prices/help; the managed device/count choices must be current.
                return array_replace($old, $field, array_intersect_key($old, array_flip(['unit_price', 'monthly_unit_price', 'help_ka', 'help_en', 'help_ru'])));
            }, $defaults['extra_fields']);
            $config['extra_fields'] = array_merge($config['extra_fields'], $fields->except(['intercoms', 'credentials'])->values()->all());
            $config['components'] = array_map(function (array $component) use ($components): array {
                $old = $components->pull($component['key'], []);

                return array_replace($component, $old, array_intersect_key($component, array_flip(['quantity_mode', 'quantity_field', 'quantity_locked', 'required', 'rules'])));
            }, $defaults['components']);
            $config['components'] = array_merge($config['components'], $components->except(['video-intercom', 'electric-lock', 'backup-power', 'access-installation'])->values()->all());
            $service->forceFill(['lead_form' => $config])->save();
        });
    }
}
