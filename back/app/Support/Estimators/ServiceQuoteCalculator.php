<?php

namespace App\Support\Estimators;

use App\Models\QuoteCatalogItem;
use App\Models\Service;
use App\Support\Calculators\CalculatorProfileBuilder;
use App\Support\Calculators\IntercomPlanner;

final class ServiceQuoteCalculator
{
    public function __construct(
        private readonly CalculatorProfileBuilder $profiles,
    ) {
    }

    /** @return array<string, mixed> */
    public function initialValues(Service $service): array
    {
        $config = $this->profiles->config($service);
        $values = [];

        foreach ((array) ($config['extra_fields'] ?? []) as $field) {
            if (! is_array($field) || blank($field['key'] ?? null)) {
                continue;
            }

            $key = (string) $field['key'];
            $type = (string) ($field['type'] ?? 'text');
            $default = $field['default'] ?? null;

            if ($default === null || $default === '') {
                if ($type === 'checkbox') {
                    $default = false;
                } elseif ($type === 'select') {
                    $default = $field['options'][0]['value'] ?? '';
                } else {
                    $default = '';
                }
            }

            $values[$key] = $default;
        }

        return $this->withDerivedValues($config, $values);
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function calculate(Service $service, array $state = []): array
    {
        $config = $this->profiles->config($service);
        $pricing = is_array($config['pricing'] ?? null) ? $config['pricing'] : [];
        $currency = strtoupper(trim((string) ($pricing['currency'] ?? 'GEL'))) ?: 'GEL';
        $values = array_replace(
            $this->initialValues($service),
            is_array($state['values'] ?? null) ? $state['values'] : [],
        );
        $values = $this->withDerivedValues($config, $values);

        $projectSize = (string) ($state['project_size'] ?? ($config['project_size_options'][0]['value'] ?? ''));
        $propertyType = (string) ($state['property_type'] ?? ($config['property_type_options'][0]['value'] ?? ''));
        $packageKey = (string) ($state['package'] ?? '');
        $componentOverrides = is_array($state['component_overrides'] ?? null)
            ? $state['component_overrides']
            : [];

        $serviceLines = [];
        $serviceSubtotal = $this->money($pricing['base_price'] ?? 0);

        if ($serviceSubtotal > 0) {
            $serviceLines[] = $this->serviceLine(
                'service-base',
                'სერვისის საბაზო საფასური',
                $serviceSubtotal,
            );
        }

        $projectOption = $this->optionByValue($config['project_size_options'] ?? [], $projectSize);
        if ($projectOption) {
            $amount = $this->money($projectOption['one_time_price'] ?? 0);
            $serviceSubtotal += $amount;
            if ($amount > 0) {
                $serviceLines[] = $this->serviceLine(
                    'project-size',
                    $this->label($config, 'project_size_label', 'პროექტის მასშტაბი').': '.$this->localized($projectOption),
                    $amount,
                );
            }
        }

        $propertyOption = $this->optionByValue($config['property_type_options'] ?? [], $propertyType);
        if ($propertyOption) {
            $amount = $this->money($propertyOption['one_time_price'] ?? 0);
            $serviceSubtotal += $amount;
            if ($amount > 0) {
                $serviceLines[] = $this->serviceLine(
                    'property-type',
                    $this->label($config, 'property_type_label', 'ობიექტის ტიპი').': '.$this->localized($propertyOption),
                    $amount,
                );
            }
        }

        $package = $this->packageByKey($config['packages'] ?? [], $packageKey);
        if ($package) {
            $amount = $this->money($package['one_time_price'] ?? 0);
            $serviceSubtotal += $amount;
            if ($amount > 0) {
                $serviceLines[] = $this->serviceLine(
                    'package',
                    'პაკეტი: '.$this->localized($package, 'title'),
                    $amount,
                );
            }
        }

        foreach ((array) ($config['extra_fields'] ?? []) as $field) {
            if (! is_array($field) || blank($field['key'] ?? null)) {
                continue;
            }

            $key = (string) $field['key'];
            $type = (string) ($field['type'] ?? 'text');
            $value = $values[$key] ?? null;
            $amount = 0.0;
            $detail = '';

            if ($type === 'number') {
                $quantity = $this->boundedNumber(
                    $value,
                    $field['min'] ?? null,
                    $field['max'] ?? null,
                );
                $amount = $quantity * $this->money($field['unit_price'] ?? 0);
                $detail = (string) $quantity;
            } elseif ($type === 'checkbox' && $this->truthy($value)) {
                $amount = $this->money($field['unit_price'] ?? 0);
                $detail = 'დიახ';
            } elseif ($type === 'select') {
                $option = $this->optionByValue($field['options'] ?? [], (string) $value);
                if ($option) {
                    $multiplierField = trim((string) ($field['price_multiplier_field'] ?? ''));
                    $multiplier = $multiplierField !== ''
                        ? max(0, (float) ($values[$multiplierField] ?? 0))
                        : 1;
                    $amount = $this->money($option['one_time_price'] ?? 0) * $multiplier;
                    $detail = $this->localized($option);
                }
            }

            if ($amount <= 0) {
                continue;
            }

            $serviceSubtotal += $amount;
            $serviceLines[] = $this->serviceLine(
                'field:'.$key,
                $this->localized($field).($detail !== '' ? ': '.$detail : ''),
                $amount,
            );
        }

        $minimumPrice = $this->money($pricing['minimum_price'] ?? 0);
        if ($serviceSubtotal < $minimumPrice) {
            $difference = $minimumPrice - $serviceSubtotal;
            $serviceSubtotal = $minimumPrice;
            $serviceLines[] = $this->serviceLine(
                'minimum-price',
                'მინიმალური პროექტის ღირებულების კორექტირება',
                $difference,
            );
        }

        $laborSubtotal = array_key_exists('labor_price', $state)
            ? $this->money($state['labor_price'])
            : $this->money($pricing['labor_price'] ?? 0);

        $catalog = QuoteCatalogItem::query()
            ->where('service_id', $service->getKey())
            ->where('is_active', true)
            ->get()
            ->keyBy('component_key');

        $compatible = $this->compatibleComponents(
            $config['components'] ?? [],
            $values,
            $projectSize,
            $propertyType,
            $packageKey,
        );

        $components = [];
        $componentSubtotal = 0.0;
        $knownCostTotal = 0.0;
        $missingCostCount = 0;
        $missingSaleCount = 0;

        foreach ($compatible as $component) {
            $key = (string) $component['key'];
            $override = is_array($componentOverrides[$key] ?? null)
                ? $componentOverrides[$key]
                : [];
            $required = (bool) ($component['required'] ?? false);
            $recommended = (bool) ($component['recommended'] ?? false);
            $selected = $required
                ? true
                : (array_key_exists('selected', $override)
                    ? $this->truthy($override['selected'])
                    : $recommended);

            $quantity = array_key_exists('quantity', $override)
                ? max(0, (float) $override['quantity'])
                : $this->componentQuantity($component, $values);

            if ($quantity <= 0) {
                continue;
            }

            /** @var QuoteCatalogItem|null $catalogItem */
            $catalogItem = $catalog->get($key);
            $purchasePrice = $this->nullableMoney(
                array_key_exists('purchase_price', $override)
                    ? $override['purchase_price']
                    : $catalogItem?->purchase_price,
            );
            $markupPercentage = array_key_exists('markup_percentage', $override)
                ? $this->percentage($override['markup_percentage'])
                : $this->percentage($catalogItem?->markup_percentage ?? 60);

            if (array_key_exists('sale_price', $override)) {
                $salePrice = $this->money($override['sale_price']);
            } elseif ($catalogItem && is_numeric($catalogItem->sale_price) && (float) $catalogItem->sale_price > 0) {
                $salePrice = $this->money($catalogItem->sale_price);
            } elseif ($purchasePrice !== null && $purchasePrice > 0) {
                $salePrice = round($purchasePrice * (1 + $markupPercentage / 100), 2);
            } else {
                $salePrice = $this->money($component['unit_price'] ?? 0);
            }

            $saleTotal = round($quantity * $salePrice, 2);
            $costTotal = $purchasePrice === null ? null : round($quantity * $purchasePrice, 2);
            $category = trim((string) ($component['category'] ?? 'other')) ?: 'other';

            if ($selected) {
                $componentSubtotal += $saleTotal;

                if ($costTotal !== null) {
                    $knownCostTotal += $costTotal;
                } elseif ($category !== 'labor') {
                    $missingCostCount++;
                }

                if ($salePrice <= 0 && (bool) ($component['quote_required'] ?? false)) {
                    $missingSaleCount++;
                }
            }

            $components[] = [
                'key' => $key,
                'category' => $category,
                'label' => $this->localized($component, 'title', $key),
                'description' => $this->localized($component, 'description'),
                'selected' => $selected,
                'required' => $required,
                'recommended' => $recommended,
                'quantity' => round($quantity, 2),
                'purchase_price' => $purchasePrice,
                'markup_percentage' => $markupPercentage,
                'sale_price' => $salePrice,
                'sale_total' => $saleTotal,
                'cost_total' => $costTotal,
                'supplier' => $catalogItem?->supplier,
                'brand' => $catalogItem?->brand,
                'model' => $catalogItem?->model,
                'warranty_months' => $catalogItem?->warranty_months,
                'price_on_request' => $salePrice <= 0 && (bool) ($component['quote_required'] ?? false),
            ];
        }

        $subtotalBeforeDiscount = round($serviceSubtotal + $laborSubtotal + $componentSubtotal, 2);
        $discountPercentage = array_key_exists('discount_percentage', $state)
            ? $this->percentage($state['discount_percentage'])
            : $this->percentage($pricing['discount_percentage'] ?? 0);
        $discountAmount = round($subtotalBeforeDiscount * ($discountPercentage / 100), 2);
        $finalTotal = round(max(0, $subtotalBeforeDiscount - $discountAmount), 2);
        $pricingComplete = $missingCostCount === 0 && $missingSaleCount === 0;
        $profitTotal = $pricingComplete
            ? round($finalTotal - $knownCostTotal, 2)
            : null;
        $grossMargin = $pricingComplete && $finalTotal > 0
            ? round(($profitTotal / $finalTotal) * 100, 2)
            : null;

        $lineItems = array_map(
            fn (array $line): array => [
                'key' => $line['key'],
                'label' => $line['label'],
                'quantity' => 1,
                'unit' => 'job',
                'unit_cost' => 0,
                'sell_unit' => $line['amount'],
                'cost_total' => 0,
                'sell_total' => $line['amount'],
            ],
            $serviceLines,
        );

        if ($laborSubtotal > 0) {
            $lineItems[] = [
                'key' => 'labor',
                'label' => 'სამუშაო / მონტაჟი',
                'quantity' => 1,
                'unit' => 'job',
                'unit_cost' => 0,
                'sell_unit' => $laborSubtotal,
                'cost_total' => 0,
                'sell_total' => $laborSubtotal,
            ];
        }

        foreach ($components as $component) {
            if (! $component['selected']) {
                continue;
            }

            $lineItems[] = [
                'key' => $component['key'],
                'label' => $component['label'],
                'quantity' => $component['quantity'],
                'unit' => $component['category'] === 'cabling' ? 'm' : 'pcs',
                'unit_cost' => $component['purchase_price'] ?? 0,
                'sell_unit' => $component['sale_price'],
                'cost_total' => $component['cost_total'] ?? 0,
                'sell_total' => $component['sale_total'],
            ];
        }

        if ($discountAmount > 0) {
            $lineItems[] = [
                'key' => 'discount',
                'label' => 'ფასდაკლება '.$discountPercentage.'%',
                'quantity' => 1,
                'unit' => 'job',
                'unit_cost' => 0,
                'sell_unit' => -$discountAmount,
                'cost_total' => 0,
                'sell_total' => -$discountAmount,
            ];
        }

        $serviceName = trim((string) ($service->name ?: $service->title ?: $service->slug));

        return [
            'service_id' => $service->getKey(),
            'service_slug' => $service->slug,
            'service_name' => $serviceName,
            'currency' => $currency,
            'values' => $values,
            'project_size' => $projectSize,
            'property_type' => $propertyType,
            'package' => $packageKey,
            'service_lines' => $serviceLines,
            'components' => $components,
            'service_subtotal' => round($serviceSubtotal, 2),
            'labor_subtotal' => round($laborSubtotal, 2),
            'component_subtotal' => round($componentSubtotal, 2),
            'subtotal_before_discount' => $subtotalBeforeDiscount,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'final_total' => $finalTotal,
            'known_cost_total' => round($knownCostTotal, 2),
            'profit_total' => $profitTotal,
            'gross_margin_percentage' => $grossMargin,
            'pricing_complete' => $pricingComplete,
            'missing_cost_count' => $missingCostCount,
            'missing_sale_count' => $missingSaleCount,
            'calculation' => [
                'quote_engine' => true,
                'service_name' => $serviceName,
                'service_slug' => $service->slug,
                'line_items' => $lineItems,
                'summary' => [
                    'სერვისი' => $serviceName,
                    'პარამეტრები' => $this->summaryText($config, $values, $projectSize, $propertyType),
                ],
                'financial' => [
                    'service_subtotal' => round($serviceSubtotal, 2),
                    'labor_subtotal' => round($laborSubtotal, 2),
                    'component_subtotal' => round($componentSubtotal, 2),
                    'subtotal_before_discount' => $subtotalBeforeDiscount,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $discountAmount,
                    'known_cost_total' => round($knownCostTotal, 2),
                    'profit_total' => $profitTotal,
                    'gross_margin_percentage' => $grossMargin,
                    'pricing_complete' => $pricingComplete,
                ],
                'missing_pricing' => [
                    'costs' => $missingCostCount,
                    'sale_prices' => $missingSaleCount,
                ],
            ],
        ];
    }

    public function syncCatalog(?int $serviceId = null): int
    {
        $services = Service::query()
            ->published()
            ->when($serviceId, fn ($query) => $query->whereKey($serviceId))
            ->get()
            ->filter(fn (Service $service): bool => $this->profiles->enabled($service));

        $created = 0;

        foreach ($services as $service) {
            $config = $this->profiles->config($service);

            foreach ((array) ($config['components'] ?? []) as $component) {
                if (! is_array($component) || blank($component['key'] ?? null)) {
                    continue;
                }

                $item = QuoteCatalogItem::query()->firstOrNew([
                    'service_id' => $service->getKey(),
                    'component_key' => (string) $component['key'],
                ]);

                if (! $item->exists) {
                    $item->fill([
                        'name' => $this->localized($component, 'title', (string) $component['key']),
                        'category' => (string) ($component['category'] ?? 'other'),
                        'sale_price' => $this->money($component['unit_price'] ?? 0) ?: null,
                        'markup_percentage' => 60,
                        'is_active' => true,
                    ]);
                    $created++;
                } else {
                    $item->name = $this->localized($component, 'title', $item->name);
                    $item->category = (string) ($component['category'] ?? $item->category);
                }

                $item->save();
            }
        }

        return $created;
    }

    /** @return array<int, array<string, mixed>> */
    private function compatibleComponents(
        mixed $components,
        array $values,
        string $projectSize,
        string $propertyType,
        string $packageKey,
    ): array {
        if (! is_array($components)) {
            return [];
        }

        $compatible = array_values(array_filter(
            $components,
            fn ($component): bool => is_array($component)
                && filled($component['key'] ?? null)
                && $this->rulesMatch(
                    $component['rules'] ?? [],
                    $values,
                    $projectSize,
                    $propertyType,
                    $packageKey,
                ),
        ));

        usort(
            $compatible,
            fn (array $a, array $b): int => ((int) ($b['priority'] ?? 0)) <=> ((int) ($a['priority'] ?? 0)),
        );

        $groups = [];
        $result = [];

        foreach ($compatible as $component) {
            $group = trim((string) ($component['exclusive_group'] ?? ''));

            if ($group !== '' && isset($groups[$group])) {
                continue;
            }

            if ($group !== '') {
                $groups[$group] = true;
            }

            if ($this->componentQuantity($component, $values) <= 0) {
                continue;
            }

            $result[] = $component;
        }

        return $result;
    }

    private function componentQuantity(array $component, array $values): float
    {
        $mode = (string) ($component['quantity_mode'] ?? 'fixed');
        $source = max(0, (float) ($values[(string) ($component['quantity_field'] ?? '')] ?? 0));
        $quantity = max(0, (float) ($component['default_quantity'] ?? 1));

        if ($mode === 'field') {
            $quantity = $source;
        } elseif ($mode === 'ceil') {
            $quantity = ceil($source / max(1, (float) ($component['units_per_component'] ?? 1)));
        }

        $minimum = max(
            (bool) ($component['required'] ?? false) ? 1 : 0,
            (float) ($component['minimum_quantity'] ?? 0),
        );
        $quantity = max($minimum, $quantity);

        if (is_numeric($component['maximum_quantity'] ?? null)) {
            $quantity = min($quantity, (float) $component['maximum_quantity']);
        }

        return round(max(0, $quantity), 2);
    }

    private function rulesMatch(
        mixed $rules,
        array $values,
        string $projectSize,
        string $propertyType,
        string $packageKey,
    ): bool {
        if (! is_array($rules)) {
            return true;
        }

        foreach ($rules as $rule) {
            if (! is_array($rule) || blank($rule['field'] ?? null)) {
                continue;
            }

            $field = (string) $rule['field'];
            $actual = match ($field) {
                'project_size' => $projectSize,
                'property_type' => $propertyType,
                'package' => $packageKey,
                default => $values[$field] ?? null,
            };
            $operator = (string) ($rule['operator'] ?? 'equals');
            $expected = $rule['value'] ?? '';

            $matches = match ($operator) {
                'not_equals' => strtolower((string) $actual) !== strtolower((string) $expected),
                'gte' => (float) $actual >= (float) $expected,
                'lte' => (float) $actual <= (float) $expected,
                'contains' => str_contains(strtolower((string) $actual), strtolower((string) $expected)),
                'truthy' => $this->truthy($actual),
                'falsy' => ! $this->truthy($actual),
                default => strtolower((string) $actual) === strtolower((string) $expected),
            };

            if (! $matches) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string, mixed> */
    private function withDerivedValues(array $config, array $values): array
    {
        if (isset($config['intercom_version'])) {
            return (new IntercomPlanner)->quantities($values);
        }

        return $values;
    }

    private function summaryText(
        array $config,
        array $values,
        string $projectSize,
        string $propertyType,
    ): string {
        $parts = [];

        if ($option = $this->optionByValue($config['project_size_options'] ?? [], $projectSize)) {
            $parts[] = $this->localized($option);
        }
        if ($option = $this->optionByValue($config['property_type_options'] ?? [], $propertyType)) {
            $parts[] = $this->localized($option);
        }

        foreach ((array) ($config['extra_fields'] ?? []) as $field) {
            if (! is_array($field) || blank($field['key'] ?? null)) {
                continue;
            }

            $key = (string) $field['key'];
            $value = $values[$key] ?? null;
            if ($value === null || $value === '' || $value === false) {
                continue;
            }

            if (($field['type'] ?? null) === 'select') {
                $option = $this->optionByValue($field['options'] ?? [], (string) $value);
                $value = $option ? $this->localized($option) : $value;
            } elseif (($field['type'] ?? null) === 'checkbox') {
                $value = $this->truthy($value) ? 'დიახ' : 'არა';
            }

            $parts[] = $this->localized($field).': '.$value;
        }

        return implode(' · ', array_slice($parts, 0, 8));
    }

    private function optionByValue(mixed $options, string $value): ?array
    {
        if (! is_array($options)) {
            return null;
        }

        foreach ($options as $option) {
            if (is_array($option) && (string) ($option['value'] ?? '') === $value) {
                return $option;
            }
        }

        return null;
    }

    private function packageByKey(mixed $packages, string $key): ?array
    {
        if (! is_array($packages) || $key === '') {
            return null;
        }

        foreach ($packages as $package) {
            if (is_array($package) && (string) ($package['key'] ?? '') === $key) {
                return $package;
            }
        }

        return null;
    }

    private function serviceLine(string $key, string $label, float $amount): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'amount' => round($amount, 2),
        ];
    }

    private function localized(array $source, string $prefix = '', string $fallback = ''): string
    {
        $keys = $prefix === ''
            ? ['ka', 'en', 'ru']
            : [$prefix.'_ka', $prefix.'_en', $prefix.'_ru'];

        foreach ($keys as $key) {
            $value = trim((string) ($source[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function label(array $source, string $prefix, string $fallback): string
    {
        return $this->localized($source, $prefix, $fallback);
    }

    private function boundedNumber(mixed $value, mixed $min, mixed $max): float
    {
        $number = is_numeric($value) ? (float) $value : 0;
        if (is_numeric($min)) {
            $number = max($number, (float) $min);
        }
        if (is_numeric($max)) {
            $number = min($number, (float) $max);
        }

        return $number;
    }

    private function truthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value !== 0.0;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    private function money(mixed $value): float
    {
        return round(max(0, is_numeric($value) ? (float) $value : 0), 2);
    }

    private function nullableMoney(mixed $value): ?float
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        return round(max(0, (float) $value), 2);
    }

    private function percentage(mixed $value): float
    {
        return round(min(1000, max(0, is_numeric($value) ? (float) $value : 0)), 2);
    }
}
