<?php

namespace App\Filament\Support;

use Illuminate\Support\Str;

final class ProjectSeoHelper
{
    /**
     * @param  array<int, array{name?: mixed, model?: mixed, quantity?: mixed}>  $equipment
     * @return array{title: string, description: string, imageAlt: string}
     */
    public static function suggest(
        ?string $name,
        ?string $headline,
        ?string $description,
        ?string $city,
        ?string $objectType,
        array $equipment = [],
    ): array {
        $baseTitle = self::clean($headline) ?: self::clean($name) ?: 'SafeTech პროექტი';
        $city = self::clean($city);
        $objectType = self::clean($objectType);
        $baseDescription = self::clean($description);

        $seoTitle = self::limit($baseTitle, 68);

        if ($city !== '' && ! self::contains($baseTitle, $city)) {
            $suffix = ' — '.$city;
            $baseLength = max(1, 68 - mb_strlen($suffix));
            $seoTitle = self::limit(self::limit($baseTitle, $baseLength).$suffix, 68);
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
            $objectType !== '' ? $objectType : null,
            $baseTitle,
            $city !== '' ? $city : null,
            'SafeTech',
        ])->filter()->unique()->values();

        return [
            'title' => $seoTitle,
            'description' => $seoDescription,
            'imageAlt' => self::limit($altParts->implode(' — '), 140),
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
