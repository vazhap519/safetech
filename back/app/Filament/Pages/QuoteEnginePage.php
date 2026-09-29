<?php

namespace App\Filament\Pages;

use App\Filament\Resources\EstimateResource;
use App\Filament\Support\NavigationGroup;
use App\Models\Estimate;
use App\Models\Service;
use App\Support\Calculators\BarrierConfigurator;
use App\Support\Calculators\BarrierQuoteProfile;
use App\Support\Calculators\CalculatorProfileBuilder;
use App\Support\Estimators\ServiceQuoteCalculator;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class QuoteEnginePage extends Page
{
    protected static ?string $navigationLabel = 'Quote Engine';

    protected static ?string $title = 'SafeTech Quote Engine';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.pages.quote-engine';

    public ?int $serviceId = null;

    public string $clientName = '';

    public string $company = '';

    public string $phone = '';

    public string $email = '';

    public string $projectTitle = '';

    public string $location = '';

    public string $internalNotes = '';

    public string $clientNote = '';

    public string $projectSize = '';

    public string $propertyType = '';

    public string $packageKey = '';

    public float $laborPrice = 0;

    public float $discountPercentage = 0;

    /** @var array<string, mixed> */
    public array $values = [];

    /** @var array<string, array<string, mixed>> */
    public array $componentOverrides = [];

    public function mount(): void
    {
        $first = $this->services()->first();

        if ($first) {
            $this->serviceId = (int) $first->getKey();
            $this->resetServiceState();
        }
    }

    public function updatedServiceId(): void
    {
        $this->resetServiceState();
    }

    /** @return array<int, string> */
    public function serviceOptions(): array
    {
        return $this->services()
            ->mapWithKeys(fn (Service $service): array => [
                (int) $service->getKey() => trim((string) ($service->name ?: $service->title ?: $service->slug)),
            ])
            ->all();
    }

    public function service(): ?Service
    {
        if (! $this->serviceId) {
            return null;
        }

        return Service::query()->find($this->serviceId);
    }

    /** @return array<string, mixed> */
    public function profile(): array
    {
        $service = $this->service();

        if (! $service) {
            return [];
        }

        if (BarrierQuoteProfile::matches((string) $service->slug, (string) $service->name)) {
            $service = clone $service;
            $service->setAttribute(
                'lead_form',
                app(ServiceQuoteCalculator::class)->config($service),
            );
        }

        return app(CalculatorProfileBuilder::class)->build($service, 'ka');
    }

    public function updatedValues(mixed $value, string $key): void
    {
        $service = $this->service();

        if (! $service || ! BarrierQuoteProfile::matches((string) $service->slug, (string) $service->name)) {
            return;
        }

        $this->values = (new BarrierConfigurator)->normalize($this->values, $key);
    }

    /** @return array<string, mixed> */
    public function quote(): array
    {
        $service = $this->service();

        if (! $service) {
            return $this->emptyQuote();
        }

        return app(ServiceQuoteCalculator::class)->calculate($service, [
            'values' => $this->values,
            'project_size' => $this->projectSize,
            'property_type' => $this->propertyType,
            'package' => $this->packageKey,
            'labor_price' => $this->laborPrice,
            'discount_percentage' => $this->discountPercentage,
            'component_overrides' => $this->componentOverrides,
        ]);
    }

    public function setComponentValue(string $key, string $field, mixed $value): void
    {
        if (! in_array($field, ['selected', 'quantity', 'purchase_price', 'markup_percentage', 'sale_price'], true)) {
            return;
        }

        $this->componentOverrides[$key] ??= [];

        if ($field === 'selected') {
            $this->componentOverrides[$key][$field] = filter_var($value, FILTER_VALIDATE_BOOLEAN);

            return;
        }

        if ($value === '' || $value === null) {
            unset($this->componentOverrides[$key][$field]);

            return;
        }

        if (is_numeric($value)) {
            $number = max(0, (float) $value);
            $this->componentOverrides[$key][$field] = $field === 'markup_percentage'
                ? min(1000, $number)
                : round($number, 2);
        }
    }

    public function syncCatalog(): void
    {
        $count = app(ServiceQuoteCalculator::class)->syncCatalog($this->serviceId);

        Notification::make()
            ->title($count > 0 ? "{$count} კატალოგის პოზიცია დაემატა" : 'კატალოგი უკვე სინქრონულია')
            ->body('არსებული ფასები და მომწოდებლის მონაცემები არ შეცვლილა.')
            ->success()
            ->send();
    }

    public function saveQuote(): mixed
    {
        $service = $this->service();

        if (! $service) {
            Notification::make()
                ->title('აირჩიეთ სერვისი')
                ->danger()
                ->send();

            return null;
        }

        $quote = $this->quote();
        $calculation = $quote['calculation'];
        $calculation['client_note'] = trim($this->clientNote);
        $calculation['project_title'] = trim($this->projectTitle);
        $calculation['location'] = trim($this->location);
        $calculation['components_internal'] = $quote['components'];

        $estimate = Estimate::query()->create([
            'client_name' => trim($this->clientName) ?: null,
            'company' => trim($this->company) ?: null,
            'email' => trim($this->email) ?: null,
            'phone' => trim($this->phone) ?: null,
            'project_type' => $this->legacyProjectType($service),
            'service_id' => $service->getKey(),
            'project_title' => trim($this->projectTitle) ?: null,
            'location' => trim($this->location) ?: null,
            'markup_rate' => 0,
            'discount_percentage' => $quote['discount_percentage'],
            'required_storage_tb' => 0,
            'cost_total' => $quote['known_cost_total'],
            'markup_total' => $quote['profit_total'] ?? 0,
            'final_total' => $quote['final_total'],
            'profit_total' => $quote['profit_total'] ?? 0,
            'manual_items' => [],
            'configuration' => [
                'values' => $quote['values'],
                'project_size' => $quote['project_size'],
                'property_type' => $quote['property_type'],
                'package' => $quote['package'],
                'labor_price' => $this->laborPrice,
            ],
            'component_overrides' => $this->componentOverrides,
            'pricing_complete' => $quote['pricing_complete'],
            'calculation' => $calculation,
            'notes' => trim($this->internalNotes) ?: null,
            'created_by' => auth()->id(),
        ]);

        Notification::make()
            ->title("შეთავაზება {$estimate->estimate_number} შეიქმნა")
            ->body($quote['pricing_complete']
                ? 'ფასები სრულად არის შევსებული. PDF მზადაა.'
                : 'შეთავაზება შენახულია, მაგრამ ზოგ კომპონენტს თვითღირებულება ან გასაყიდი ფასი აკლია.')
            ->success()
            ->send();

        return redirect(EstimateResource::getUrl('edit', ['record' => $estimate]));
    }

    public function clientText(): string
    {
        $quote = $this->quote();
        $lines = [
            'SafeTech — კომერციული შეთავაზება',
        ];

        if (trim($this->projectTitle) !== '') {
            $lines[] = 'პროექტი: '.trim($this->projectTitle);
        }

        $lines[] = 'სერვისი: '.($quote['service_name'] ?? '—');

        if (trim($this->location) !== '') {
            $lines[] = 'ობიექტი: '.trim($this->location);
        }

        $lines[] = '';

        foreach ($quote['calculation']['line_items'] ?? [] as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $total = (float) ($item['sell_total'] ?? 0);
            $lines[] = sprintf(
                '%s — %s × %.2f ₾ = %.2f ₾',
                (string) ($item['label'] ?? ''),
                rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.'),
                (float) ($item['sell_unit'] ?? 0),
                $total,
            );
        }

        $lines[] = '';
        $lines[] = 'სულ: '.number_format((float) ($quote['final_total'] ?? 0), 2, '.', ' ').' ₾';

        if (trim($this->clientNote) !== '') {
            $lines[] = trim($this->clientNote);
        }

        $lines[] = 'საბოლოო ღირებულება დასტურდება ტექნიკური შეფასებისა და საბოლოო კომპლექტაციის შემდეგ.';

        return implode("\n", $lines);
    }

    private function resetServiceState(): void
    {
        $service = $this->service();

        if (! $service) {
            $this->values = [];
            $this->projectSize = '';
            $this->propertyType = '';
            $this->packageKey = '';
            $this->laborPrice = 0;
            $this->discountPercentage = 0;
            $this->componentOverrides = [];

            return;
        }

        $profile = $this->profile();
        $config = app(ServiceQuoteCalculator::class)->config($service);
        $pricing = is_array($config['pricing'] ?? null) ? $config['pricing'] : [];

        $this->values = app(ServiceQuoteCalculator::class)->initialValues($service);
        $this->projectSize = (string) ($profile['projectSize']['options'][0]['value'] ?? '');
        $this->propertyType = (string) ($profile['propertyType']['options'][0]['value'] ?? '');
        $this->packageKey = (string) (
            collect($profile['packages'] ?? [])->firstWhere('recommended', true)['key']
            ?? ($profile['packages'][0]['key'] ?? '')
        );
        $this->laborPrice = round(max(0, (float) ($pricing['labor_price'] ?? 0)), 2);
        $this->discountPercentage = round(min(100, max(0, (float) ($pricing['discount_percentage'] ?? 0))), 2);
        $this->componentOverrides = [];
    }

    private function services()
    {
        $builder = app(CalculatorProfileBuilder::class);

        return Service::query()
            ->published()
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Service $service): bool => $builder->enabled($service))
            ->values();
    }

    private function legacyProjectType(Service $service): string
    {
        $haystack = mb_strtolower($service->slug.' '.$service->name);

        return match (true) {
            str_contains($haystack, 'cctv'), str_contains($haystack, 'camera'), str_contains($haystack, 'კამერ') => 'cctv',
            str_contains($haystack, 'network'), str_contains($haystack, 'wifi'), str_contains($haystack, 'ქსელ') => 'network',
            default => 'service',
        };
    }

    /** @return array<string, mixed> */
    private function emptyQuote(): array
    {
        return [
            'service_name' => '',
            'components' => [],
            'calculation' => ['line_items' => []],
            'final_total' => 0,
            'known_cost_total' => 0,
            'profit_total' => null,
            'gross_margin_percentage' => null,
            'discount_percentage' => 0,
            'pricing_complete' => false,
            'missing_cost_count' => 0,
            'missing_sale_count' => 0,
        ];
    }
}
