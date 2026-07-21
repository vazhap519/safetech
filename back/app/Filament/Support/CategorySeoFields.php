<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

final class CategorySeoFields
{
    /** @return array<int, Section> */
    public static function sections(): array
    {
        return [
            Section::make('SEO და ინდექსაცია')
                ->description('ძირითადი ქართული მნიშვნელობები გამოიყენება მაშინაც, როცა კონკრეტული ენის ველი ცარიელია.')
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO სათაური')
                        ->maxLength(255)
                        ->helperText('საძიებო შედეგში სათაური შეიძლება მოწყობილობის მიხედვით შეიკვეცოს; მთავარი აზრი დასაწყისში მოათავსეთ.'),
                    Textarea::make('seo_description')
                        ->label('Meta აღწერა')
                        ->rows(3)
                        ->maxLength(320)
                        ->helperText('დაწერეთ ბუნებრივი, უნიკალური აღწერა და მკაფიო სარგებელი.')
                        ->columnSpanFull(),
                    TagsInput::make('seo_keywords')
                        ->label('თემატური საკვანძო სიტყვები')
                        ->separator(',')
                        ->helperText('Google meta keywords-ს ranking-ისთვის არ იყენებს; ეს ველი შიდა კონტენტისა და სხვა არხებისთვისაა.')
                        ->columnSpanFull(),
                    RichEditor::make('intro_text')
                        ->label('კატეგორიის შესავალი ტექსტი')
                        ->columnSpanFull(),
                    self::faq('faq', 'ხშირად დასმული კითხვები')
                        ->columnSpanFull(),
                    self::json('schema', 'Custom Schema JSON-LD')
                        ->columnSpanFull(),
                    Toggle::make('noindex')
                        ->label('საძიებო სისტემებში არ გამოჩნდეს')
                        ->helperText('ჩართეთ მხოლოდ დროებითი, დუბლირებული ან ძალიან მცირე შინაარსის მქონე გვერდისთვის.')
                        ->default(false),
                ])
                ->columns(2),
            ...array_map(
                fn (string $locale, string $label): Section => self::translationSection($locale, $label),
                array_keys(self::locales()),
                array_values(self::locales()),
            ),
        ];
    }

    private static function translationSection(string $locale, string $label): Section
    {
        return Section::make("კონტენტი და SEO - {$label}")
            ->collapsed($locale !== 'ka')
            ->schema([
                TextInput::make("translations.fields.name.{$locale}")
                    ->label('კატეგორიის სახელი')
                    ->maxLength(255),
                TextInput::make("translations.fields.seo_title.{$locale}")
                    ->label('SEO სათაური')
                    ->maxLength(255),
                Textarea::make("translations.fields.seo_description.{$locale}")
                    ->label('Meta აღწერა')
                    ->rows(3)
                    ->maxLength(320)
                    ->columnSpanFull(),
                TagsInput::make("translations.keywords.{$locale}")
                    ->label('თემატური საკვანძო სიტყვები')
                    ->separator(',')
                    ->columnSpanFull(),
                RichEditor::make("translations.fields.intro_text.{$locale}")
                    ->label('შესავალი ტექსტი')
                    ->columnSpanFull(),
                self::faq("translations.faq.{$locale}", 'ხშირად დასმული კითხვები')
                    ->columnSpanFull(),
                self::json("translations.schema.{$locale}", 'Custom Schema JSON-LD')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function faq(string $name, string $label): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema([
                TextInput::make('question')->label('კითხვა')->required(),
                Textarea::make('answer')->label('პასუხი')->required()->rows(3),
            ])
            ->collapsible()
            ->cloneable()
            ->default([]);
    }

    private static function json(string $name, string $label): Textarea
    {
        return Textarea::make($name)
            ->label($label)
            ->rows(8)
            ->helperText('სავალდებულო არ არის. შეავსეთ მხოლოდ ვალიდური JSON ობიექტით.')
            ->formatStateUsing(fn (mixed $state): mixed => is_array($state)
                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : $state)
            ->dehydrateStateUsing(function (mixed $state): mixed {
                if (blank($state) || is_array($state)) {
                    return blank($state) ? null : $state;
                }

                $decoded = json_decode((string) $state, true);

                return json_last_error() === JSON_ERROR_NONE ? $decoded : $state;
            })
            ->rules([
                fn (): callable => function (string $attribute, mixed $value, callable $fail): void {
                    if (blank($value) || is_array($value)) {
                        return;
                    }

                    json_decode((string) $value, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $fail('Schema JSON არასწორი ფორმატითაა შევსებული.');
                    }
                },
            ]);
    }

    /** @return array<string, string> */
    private static function locales(): array
    {
        return [
            'ka' => 'ქართული',
            'en' => 'English',
            'ru' => 'Русский',
        ];
    }
}
