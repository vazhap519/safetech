<?php

namespace App\Filament\Support;

use App\Support\SocialLinks;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

final class CategoryFields
{
    public static function core(bool $withAppearance = false, string $kind = 'service'): Section
    {
        return Section::make('Category name (KA / EN / RU)')
            ->description('ახალი კატეგორიის შექმნისას შეავსეთ სამივე ენა. არსებულ ჩანაწერზე ცარიელი ინგლისური ან რუსული მნიშვნელობა უსაფრთხოდ გამოიყენებს ქართულ სათაურს.')
            ->schema([
                TextInput::make('name')
                    ->label('Category name (ქართული)')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(self::syncGeorgianNameAndSlug($kind)),
                ...LocalizedContentFields::secondaryInputs(
                    'name',
                    'Category name',
                    maxLength: 255,
                    required: fn (?Model $record): bool => $record === null,
                ),
                TextInput::make('slug')
                    ->label('URL slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->live()
                    ->readOnly()
                    ->afterStateHydrated(function (?string $state, Get $get, Set $set) use ($kind): void {
                        $slug = trim((string) $state);

                        if ($slug !== '' && trim((string) $get('translations.seo.canonical')) === '') {
                            $set('translations.seo.canonical', self::canonicalFor($kind, $slug));
                        }
                    })
                    ->helperText('ავტომატურად გენერირდება ქართული კატეგორიის სახელიდან.'),
                ...($withAppearance ? [
                    ColorPicker::make('color')
                        ->label('Color')
                        ->default('#00C2A8'),
                    Select::make('icon')
                        ->label('Icon')
                        ->options(AdminIconOptions::content())
                        ->searchable()
                        ->preload()
                        ->helperText('Choose an icon instead of typing it manually.'),
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0),
                ] : []),
            ])
            ->columns(2);
    }

    private static function syncGeorgianNameAndSlug(string $kind): \Closure
    {
        return function (
            ?string $state,
            ?string $old,
            Get $get,
            Set $set,
            ?Model $record,
        ): void {
            $set('translations.fields.name.ka', $state);

            $currentSlug = trim((string) $get('slug'));
            $previousGeneratedSlug = StableSlug::fromTitle((string) $old);
            $recordSlug = trim((string) $record?->slug);
            $nextSlug = $currentSlug;

            if ($currentSlug === '' || $currentSlug === $previousGeneratedSlug || ($record !== null && $currentSlug === $recordSlug)) {
                $nextSlug = StableSlug::fromTitle((string) $state);
                $set('slug', $nextSlug);
            }

            $currentCanonical = trim((string) $get('translations.seo.canonical'));
            $previousCanonical = $previousGeneratedSlug !== ''
                ? self::canonicalFor($kind, $previousGeneratedSlug)
                : '';

            if ($currentCanonical === '' || $currentCanonical === $previousCanonical) {
                $set('translations.seo.canonical', self::canonicalFor($kind, $nextSlug));
            }
        };
    }

    private static function canonicalFor(string $kind, string $slug): string
    {
        $prefix = $kind === 'project' ? '/projects/category/' : '/services/category/';

        return SocialLinks::frontendUrl($prefix.ltrim($slug, '/'));
    }
}
