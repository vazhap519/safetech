<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationLabel = 'სერვისები';
    protected static ?string $modelLabel = 'სერვისი';
    protected static ?string $pluralModelLabel = 'სერვისები';

    public static function form(Schema $schema): Schema
    {
        $card = fn (bool $featured = false) => [
            TextInput::make('icon')->label('აიკონი'), TextInput::make('title')->label('სათაური')->required(),
            Textarea::make('description')->label('აღწერა')->required(),
            ...($featured ? [Toggle::make('featured')->label('გამორჩეული')] : []),
        ];

        return $schema->components([
            TextInput::make('name')->label('სახელი')->required()->maxLength(255),
            TextInput::make('slug')->required()->unique(ignoreRecord: true),
            TextInput::make('eyebrow')->label('კატეგორია'),
            TextInput::make('icon')->label('Material icon')->default('settings')->required(),
            TextInput::make('title')->label('მთავარი სათაური')->required(),
            Textarea::make('description')->label('მოკლე აღწერა')->required()->rows(3),
            Textarea::make('seo_description')->label('SEO აღწერა')->required()->rows(3)->maxLength(320),
            FileUpload::make('hero_image')->label('მთავარი ფოტო')->image()->disk('public')->directory('services'),
            TagsInput::make('keywords')->label('SEO საკვანძო სიტყვები'),
            TagsInput::make('highlights')->label('მთავარი უპირატესობები'),
            TagsInput::make('industries')->label('ინდუსტრიები'),
            TagsInput::make('brands')->label('ბრენდები'),
            Repeater::make('benefits')->label('სარგებელი')->schema($card())->columns(3)->collapsible(),
            Repeater::make('solutions')->label('გადაწყვეტილებები')->schema($card(true))->columns(3)->collapsible(),
            Repeater::make('process')->label('სამუშაო პროცესი')->schema([
                TextInput::make('title')->label('ეტაპი')->required(), Textarea::make('description')->label('აღწერა')->required(),
            ])->columns(2)->collapsible(),
            Textarea::make('overview')->label('Overview JSON')->rule('json')->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $state)->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)->helperText('სტრუქტურირებული overview ობიექტი JSON ფორმატში.'),
            Textarea::make('warranty')->label('გარანტია'),
            Textarea::make('sla')->label('SLA პირობები'),
            Toggle::make('is_published')->label('გამოქვეყნებულია')->default(false),
            TextInput::make('sort_order')->label('რიგითობა')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('სახელი')->searchable()->sortable(),
            TextColumn::make('slug')->searchable(),
            IconColumn::make('is_published')->label('გამოქვეყნებული')->boolean(),
            TextColumn::make('sort_order')->label('რიგი')->sortable(),
            TextColumn::make('updated_at')->label('განახლდა')->dateTime()->sortable(),
        ])->defaultSort('sort_order')->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListServices::route('/'), 'create' => Pages\CreateService::route('/create'), 'edit' => Pages\EditService::route('/{record}/edit')];
    }
}
