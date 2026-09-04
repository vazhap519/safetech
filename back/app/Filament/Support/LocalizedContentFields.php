<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

final class LocalizedContentFields
{
    private const LOCALE_LABELS = [
        'ka' => 'ქართული',
        'en' => 'ინგლისური',
        'ru' => 'რუსული',
    ];

    private const SECONDARY_LOCALE_LABELS = [
        'en' => 'ინგლისური',
        'ru' => 'რუსული',
    ];

    /** @return array<int, TextInput|Textarea> */
    public static function inputs(
        string $field,
        string $label,
        bool $textarea = false,
        int $rows = 3,
        ?int $maxLength = null,
        bool $live = false,
    ): array {
        return collect(self::LOCALE_LABELS)
            ->map(function (string $localeLabel, string $locale) use (
                $field,
                $label,
                $textarea,
                $rows,
                $maxLength,
                $live,
            ): TextInput|Textarea {
                $component = $textarea
                    ? Textarea::make("translations.fields.{$field}.{$locale}")->rows($rows)
                    : TextInput::make("translations.fields.{$field}.{$locale}");

                $component->label(self::labelFor($field, $label, $localeLabel));

                if ($helperText = self::helperTextFor($field)) {
                    $component
                        ->helperText($helperText)
                        ->placeholder('ცარიელი დატოვებისას ავტომატურად შეივსება');
                }

                if ($maxLength !== null) {
                    $component->maxLength($maxLength);
                }

                if ($live) {
                    $component->live(onBlur: true);
                }

                return $component;
            })
            ->values()
            ->all();
    }

    /**
     * EN/RU inputs for a top-level model translation field. The Georgian
     * value remains in the model's normal fallback column and is not repeated.
     *
     * @return array<int, TextInput|Textarea>
     */
    public static function secondaryInputs(
        string $field,
        string $label,
        bool $textarea = false,
        int $rows = 3,
        ?int $maxLength = null,
        bool|\Closure $required = false,
    ): array {
        return collect(self::SECONDARY_LOCALE_LABELS)
            ->map(function (string $localeLabel, string $locale) use (
                $field,
                $label,
                $textarea,
                $rows,
                $maxLength,
                $required,
            ): TextInput|Textarea {
                $component = $textarea
                    ? Textarea::make("translations.fields.{$field}.{$locale}")->rows($rows)
                    : TextInput::make("translations.fields.{$field}.{$locale}");

                $component
                    ->label("{$label} ({$localeLabel})")
                    ->placeholder('ცარიელი დატოვებისას გამოყენებული იქნება ქართული ტექსტი');

                if ($maxLength !== null) {
                    $component->maxLength($maxLength);
                }

                if ($required !== false) {
                    $component->required($required);
                }

                return $component;
            })
            ->values()
            ->all();
    }

    /**
     * Translation inputs intended to live inside a JSON repeater item.
     * Georgian remains the repeater's base/fallback value; EN/RU are stored
     * alongside it under translations.{locale}.{field}.
     *
     * @return array<int, TextInput|Textarea>
     */
    public static function itemInputs(
        string $field,
        string $label,
        bool $textarea = false,
        int $rows = 3,
        ?int $maxLength = null,
    ): array {
        return collect(self::SECONDARY_LOCALE_LABELS)
            ->map(function (string $localeLabel, string $locale) use (
                $field,
                $label,
                $textarea,
                $rows,
                $maxLength,
            ): TextInput|Textarea {
                $component = $textarea
                    ? Textarea::make("translations.{$locale}.{$field}")->rows($rows)
                    : TextInput::make("translations.{$locale}.{$field}");

                $component
                    ->label("{$label} ({$localeLabel})")
                    ->placeholder('ცარიელი დატოვებისას გამოყენებული იქნება ქართული ტექსტი');

                if ($maxLength !== null) {
                    $component->maxLength($maxLength);
                }

                return $component;
            })
            ->values()
            ->all();
    }

    public static function customEntries(string $helperText): Repeater
    {
        return Repeater::make('translations.entries')
            ->label('Legacy translations (compatibility only)')
            ->helperText('ძველი პროექტების ხელით დამატებული გასაღებები თავსებადობისთვის შენარჩუნებულია. ახალი პროექტის სექციებისთვის გამოიყენეთ EN/RU ველები უშუალოდ შესაბამის ბლოკში.')
            ->schema([
                TextInput::make('key')
                    ->label('გასაღები')
                    ->required()
                    ->helperText($helperText),
                TextInput::make('ka')->label('ქართული'),
                TextInput::make('en')->label('ინგლისური'),
                TextInput::make('ru')->label('რუსული'),
            ])
            ->columns(2)
            ->default([])
            ->collapsible()
            ->collapsed()
            ->reorderable();
    }

    private static function labelFor(string $field, string $fallback, string $localeLabel): string
    {
        $label = match ($field) {
            'card.title' => 'სერვისების სიის ბარათის სათაური',
            'card.description' => 'სერვისების სიის ბარათის მოკლე აღწერა',
            default => $fallback,
        };

        return "{$label} ({$localeLabel})";
    }

    private static function helperTextFor(string $field): ?string
    {
        return match ($field) {
            'card.title' => 'ეს ტექსტი ჩანს /services გვერდზე სერვისის პატარა ბარათზე. ცარიელი დატოვებისას ავტომატურად გამოიყენება შესაბამისი ენის სერვისის სახელი.',
            'card.description' => 'ეს მოკლე ტექსტი ჩანს /services გვერდის ბარათზე სათაურის ქვემოთ. ცარიელი დატოვებისას ავტომატურად გამოიყენება შესაბამისი ენის მოკლე აღწერა.',
            default => null,
        };
    }
}
