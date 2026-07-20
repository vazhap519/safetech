<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;

final class StructuredDataJsonField
{
    public static function make(string $helperText): Textarea
    {
        return Textarea::make('schema')
            ->label('ინდივიდუალური Schema JSON-LD')
            ->helperText($helperText)
            ->rows(10)
            ->formatStateUsing(fn ($state) => is_array($state)
                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : $state)
            ->dehydrateStateUsing(function ($state): ?array {
                if (blank($state)) {
                    return null;
                }

                $decoded = json_decode((string) $state, true);

                return is_array($decoded) ? $decoded : null;
            })
            ->rules([
                fn () => function (string $attribute, $value, $fail): void {
                    if (blank($value)) {
                        return;
                    }

                    json_decode((string) $value, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $fail('Schema JSON არასწორია: '.json_last_error_msg());
                    }
                },
            ])
            ->columnSpanFull();
    }
}
