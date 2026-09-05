<?php

namespace App\Filament\Support;

use Illuminate\Support\Str;

final class ProjectSeoHelper
{
    /**
     * @param  array<int, array{name?: mixed, model?: mixed, quantity?: mixed}>  $equipment
     * @param  array<int, mixed>  $services
     * @return array{title: string, description: string, imageAlt: string, keywords: array<int, string>}
     */
    public static function suggest(
        ?string $name,
        ?string $headline,
        ?string $description,
        ?string $city,
        ?string $objectType,
        array $equipment = [],
        array $services = [],
    ): array {
        $baseTitle = self::clean($headline) ?: self::clean($name) ?: 'SafeTech პროექტი';
        $city = self::clean($city);
        $objectType = self::clean($objectType);
        $baseDescription = self::clean($description);
        $serviceNames = collect($services)
            ->map(fn ($service): string => self::clean($service))
            ->filter(fn (string $service): bool => $service !== '')
            ->unique(fn (string $service): string => Str::lower($service))
            ->values();
        $primaryService = $serviceNames->first() ?? '';

        $titleCore = collect([
            $baseTitle,
            $primaryService !== '' && ! self::contains($baseTitle, $primaryService) ? $primaryService : null,
        ])->filter()->implode(' — ');
        $seoTitle = self::limit($titleCore, 68);

        if ($city !== '' && ! self::contains($titleCore, $city)) {
            $suffix = ' — '.$city;
            $baseLength = max(1, 68 - mb_strlen($suffix));
            $seoTitle = self::limit(self::limit($titleCore, $baseLength).$suffix, 68);
        }

        $equipmentNames = collect($equipment)
            ->filter(fn ($item): bool => is_array($item) && self::clean($item['name'] ?? null) !== '')
            ->map(function (array $item): string {
                $name = self::clean($item['name'] ?? null);
                $model = self::clean($item['model'] ?? null);
                $quantity = self::clean($item['quantity'] ?? null);

                return collect([$name, $model, $quantity])
                    ->filter(fn (string $value): bool => $value !== '')
                    ->implode(' ');
            })
            ->take(3)
            ->values();

        $context = collect([
            $primaryService !== '' ? $primaryService : null,
            $objectType !== '' ? $objectType : null,
            $city !== '' ? $city : null,
        ])->filter()->implode(', ');

        $descriptionParts = [];

        if ($context !== '') {
            $descriptionParts[] = "SafeTech-ის შესრულებული პროექტი — {$context}.";
        } else {
            $descriptionParts[] = 'SafeTech-ის შესრულებული პროექტი.';
        }

        if ($equipmentNames->isNotEmpty()) {
            $descriptionParts[] = 'გამოყენებული ტექნიკა: '.$equipmentNames->implode(', ').'.';
        } elseif ($baseDescription !== '') {
            $descriptionParts[] = $baseDescription;
        }

        $descriptionParts[] = 'პროფესიონალური დაგეგმვა, მონტაჟი და გამართვა.';
        $seoDescription = self::limit(implode(' ', $descriptionParts), 170);

        $altParts = collect([
            $primaryService !== '' ? $primaryService : null,
            $objectType !== '' ? $objectType : null,
            $baseTitle,
            $city !== '' ? $city : null,
            'SafeTech',
        ])->filter()->unique()->values();

        $keywords = collect([
            ...$serviceNames->all(),
            $city !== '' ? $city : null,
            $objectType !== '' ? $objectType : null,
            ...collect($equipment)
                ->filter(fn ($item): bool => is_array($item))
                ->map(fn (array $item): string => self::clean($item['name'] ?? null))
                ->filter()
                ->take(5)
                ->all(),
        ])
            ->filter(fn ($value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->unique(fn (string $value): string => Str::lower($value))
            ->take(12)
            ->values()
            ->all();

        return [
            'title' => $seoTitle,
            'description' => $seoDescription,
            'imageAlt' => self::limit($altParts->implode(' — '), 140),
            'keywords' => $keywords,
        ];
    }

    private static function clean(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    private static function contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && Str::contains(Str::lower($haystack), Str::lower($needle));
    }

    private static function limit(string $value, int $length): string
    {
        return Str::limit($value, $length, '');
    }
}
