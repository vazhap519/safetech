<?php

namespace App\Support\Calculators;

use App\Models\Service;
use App\Support\MultilingualContent;

final class CalculatorProfileBuilder
{
    public function enabled(Service $service): bool
    {
        $config = $this->config($service);

        return $config !== [] && (bool) ($config['calculator_enabled'] ?? true);
    }

    /** @return array<string, mixed> */
    public function build(Service $service, string $locale): array
    {
        $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
        $config = $this->config($service);
        $pricing = is_array($config['pricing'] ?? null) ? $config['pricing'] : [];

        return [
            'serviceId' => $service->getKey(),
            'slug' => $service->slug,
            'name' => $this->serviceValue($service, 'name', $locale, $service->name ?: $service->title),
            'description' => $this->serviceValue($service, 'description', $locale, $service->description),
            'icon' => $service->icon ?: 'settings',
            'currency' => strtoupper($this->string($pricing['currency'] ?? 'GEL')),
            'basePrice' => $this->money($pricing['base_price'] ?? 0),
            'monthlyBasePrice' => $this->money($pricing['monthly_base_price'] ?? 0),
            'minimumPrice' => $this->money($pricing['minimum_price'] ?? 0),
            'projectSize' => [
                'label' => $this->localizedLabel($config, 'project_size_label', $locale, 'Project size'),
                'options' => $this->options($config['project_size_options'] ?? [], $locale),
            ],
            'propertyType' => [
                'label' => $this->localizedLabel($config, 'property_type_label', $locale, 'Property type'),
                'options' => $this->options($config['property_type_options'] ?? [], $locale),
            ],
            'fields' => $this->fields($config['extra_fields'] ?? [], $locale),
            'packages' => $this->packages($config['packages'] ?? [], $locale),
            'disclaimer' => $this->localizedLabel(
                $config,
                'calculator_disclaimer',
                $locale,
                'The result is indicative. The final price is confirmed after a technical assessment.',
            ),
        ];
    }

    /** @return array<string, mixed> */
    private function config(Service $service): array
    {
        return is_array($service->lead_form) ? $service->lead_form : [];
    }

    private function serviceValue(
        Service $service,
        string $field,
        string $locale,
        mixed $fallback,
    ): string {
        $values = MultilingualContent::valuesForField($service, $field, $fallback);

        return $this->string($values[$locale] ?? $values['ka'] ?? $fallback);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function options(mixed $options, string $locale): array
    {
        if (! is_array($options)) {
            return [];
        }

        return collect($options)
            ->filter(fn ($option): bool => is_array($option) && filled($option['value'] ?? null))
            ->map(fn (array $option): array => [
                'value' => $this->string($option['value']),
                'label' => $this->localizedValue($option, $locale, $this->string($option['value'])),
                'oneTimePrice' => $this->money($option['one_time_price'] ?? 0),
                'monthlyPrice' => $this->money($option['monthly_price'] ?? 0),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(mixed $fields, string $locale): array
    {
        if (! is_array($fields)) {
            return [];
        }

        $allowedTypes = ['text', 'number', 'textarea', 'select', 'checkbox'];

        return collect($fields)
            ->filter(fn ($field): bool => is_array($field) && filled($field['key'] ?? null))
            ->map(function (array $field) use ($allowedTypes, $locale): array {
                $type = in_array($field['type'] ?? null, $allowedTypes, true)
                    ? $field['type']
                    : 'text';

                return [
                    'key' => $this->string($field['key']),
                    'type' => $type,
                    'required' => (bool) ($field['required'] ?? false),
                    'label' => $this->localizedValue($field, $locale, $this->string($field['key'])),
                    'placeholder' => $this->localizedLabel($field, 'placeholder', $locale),
                    'help' => $this->localizedLabel($field, 'help', $locale),
                    'unit' => $this->localizedLabel($field, 'unit', $locale),
                    'min' => $this->numberOrNull($field['min'] ?? null),
                    'max' => $this->numberOrNull($field['max'] ?? null),
                    'step' => $this->numberOrNull($field['step'] ?? null),
                    'defaultValue' => $field['default'] ?? ($type === 'checkbox' ? false : ''),
                    'unitPrice' => $this->money($field['unit_price'] ?? 0),
                    'monthlyUnitPrice' => $this->money($field['monthly_unit_price'] ?? 0),
                    'priceMultiplierField' => $this->string($field['price_multiplier_field'] ?? ''),
                    'options' => $this->options($field['options'] ?? [], $locale),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function packages(mixed $packages, string $locale): array
    {
        if (! is_array($packages)) {
            return [];
        }

        return collect($packages)
            ->filter(fn ($package): bool => is_array($package) && filled($package['key'] ?? null))
            ->map(fn (array $package): array => [
                'key' => $this->string($package['key']),
                'title' => $this->localizedLabel($package, 'title', $locale, $this->string($package['key'])),
                'description' => $this->localizedLabel($package, 'description', $locale),
                'oneTimePrice' => $this->money($package['one_time_price'] ?? 0),
                'monthlyPrice' => $this->money($package['monthly_price'] ?? 0),
                'recommended' => (bool) ($package['recommended'] ?? false),
            ])
            ->values()
            ->all();
    }

    /** @param array<string, mixed> $source */
    private function localizedLabel(
        array $source,
        string $prefix,
        string $locale,
        string $fallback = '',
    ): string {
        return $this->localizedValue([
            'ka' => $source["{$prefix}_ka"] ?? null,
            'en' => $source["{$prefix}_en"] ?? null,
            'ru' => $source["{$prefix}_ru"] ?? null,
        ], $locale, $fallback);
    }

    /** @param array<string, mixed> $values */
    private function localizedValue(array $values, string $locale, string $fallback = ''): string
    {
        foreach ([$locale, 'ka', 'en', 'ru'] as $candidate) {
            $value = $this->string($values[$candidate] ?? '');

            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function string(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function money(mixed $value): float
    {
        return round(max(0, is_numeric($value) ? (float) $value : 0), 2);
    }

    private function numberOrNull(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
