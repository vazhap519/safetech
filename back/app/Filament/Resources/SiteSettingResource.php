<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationLabel = 'პარამეტრები';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('key')
                ->label('გასაღები')
                ->options([
                    'contact' => 'კონტაქტი',
                    'socials' => 'სოციალური ქსელები',
                    'branding' => 'ბრენდინგი',
                    'seo' => 'SEO',
                    'translations' => 'თარგმანები',
                ])
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('კონტაქტი, სოციალური ქსელები და ბრენდინგი პირდაპირ გამოიყენება ფრონტზე.'),
            Select::make('group')
                ->label('ჯგუფი')
                ->options(['general' => 'General'])
                ->default('general')
                ->required(),

            Section::make('კონტაქტი')
                ->schema([
                    TextInput::make('value.phone')
                        ->label('ტელეფონი')
                        ->required(),
                    TextInput::make('value.email')
                        ->label('ელფოსტა')
                        ->email()
                        ->required(),
                    TextInput::make('value.lead_email')
                        ->label('ფორმების მიმღები ელფოსტა')
                        ->email()
                        ->helperText('ყველა მოთხოვნის დეტალები ამ მისამართზე გაიგზავნება. თუ production გარემოში LEADS_NOTIFICATION_EMAIL არის მითითებული, პრიორიტეტი მას აქვს.')
                        ->default('safetechgeorgia@gmail.com'),
                    TextInput::make('value.whatsapp')
                        ->label('WhatsApp ნომერი')
                        ->helperText('მიუთითეთ ნომერი საერთაშორისო ფორმატში, მაგალითად 995599123456.'),
                    TextInput::make('value.whatsapp_message')
                        ->label('WhatsApp საწყისი ტექსტი'),
                    TextInput::make('value.hours')
                        ->label('სამუშაო საათები'),
                    Textarea::make('value.address')
                        ->label('მისამართი')
                        ->rows(2)
                        ->required(),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'contact'),

            Section::make('სოციალური ქსელები')
                ->schema([
                    Repeater::make('value.links')
                        ->label('Footer სოციალური ბმულები')
                        ->schema([
                            Select::make('network')
                                ->label('ქსელი')
                                ->options([
                                    'facebook' => 'Facebook',
                                    'linkedin' => 'LinkedIn',
                                    'instagram' => 'Instagram',
                                    'tiktok' => 'TikTok',
                                    'x' => 'X',
                                    'youtube' => 'YouTube',
                                    'telegram' => 'Telegram',
                                    'whatsapp' => 'WhatsApp',
                                    'email' => 'Email',
                                ])
                                ->required(),
                            TextInput::make('label')
                                ->label('სათაური')
                                ->helperText('მაგ: Facebook, X, WhatsApp'),
                            TextInput::make('href')
                                ->label('ბმული ან მნიშვნელობა')
                                ->required()
                                ->helperText('Email-სთვის ელფოსტა, WhatsApp-სთვის ნომერი, სხვა შემთხვევებში სრული URL ან დომენი.'),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->reorderable(),
                ])
                ->visible(fn (Get $get): bool => $get('key') === 'socials'),

            Section::make('ბრენდინგი')
                ->schema([
                    TextInput::make('value.site_name')
                        ->label('საიტის სახელი')
                        ->required(),
                    TextInput::make('value.tagline')
                        ->label('სლოგანი')
                        ->helperText('გამოჩნდება footer-ში ტექსტურად.'),
                    FileUpload::make('value.logo')
                        ->label('Header ლოგო')
                        ->image()
                        ->disk('public')
                        ->directory('branding'),
                    FileUpload::make('value.footer_logo')
                        ->label('Footer ლოგო')
                        ->image()
                        ->disk('public')
                        ->directory('branding'),
                    FileUpload::make('value.favicon')
                        ->label('Favicon / App Icon')
                        ->image()
                        ->disk('public')
                        ->directory('branding'),
                    FileUpload::make('value.default_image')
                        ->label('საერთო fallback სურათი')
                        ->image()
                        ->disk('public')
                        ->directory('branding')
                        ->helperText('გამოიყენება ლოგოს გარდა იმ საერთო ვიზუალებში და preview-ებში, სადაც კონკრეტული სურათი არ არის შევსებული.'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'branding'),

            KeyValue::make('value')
                ->label('SEO მნიშვნელობები')
                ->helperText('გამოიყენეთ legacy SEO პარამეტრებისთვის, მაგალითად site_name ან default_image.')
                ->visible(fn (Get $get): bool => $get('key') === 'seo'),

            Section::make('ინტერფეისის თარგმანები')
                ->schema([
                    Repeater::make('value.entries')
                        ->label('Translation keys')
                        ->schema([
                            TextInput::make('key')
                                ->label('Key')
                                ->required()
                                ->helperText('მაგ: nav.home, meta.home.title, meta.home.description, services.hero.eyebrow, project.slug.card.title'),
                            TextInput::make('ka')
                                ->label('ქართული'),
                            TextInput::make('en')
                                ->label('English'),
                            TextInput::make('ru')
                                ->label('Русский'),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->helperText('აქედან შეგიძლიათ მართოთ UI ტექსტები, navigation labels, გვერდების SEO title/description და საერთო ტექსტები 3 ენაზე. სერვისებისა და პროექტების record-level თარგმანები ავტომატურად დაემატება ამ map-ს.'),
                ])
                ->visible(fn (Get $get): bool => $get('key') === 'translations'),

            Toggle::make('is_public')
                ->label('Public API-ში გამოჩენა')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('გასაღები')->searchable()->sortable(),
                TextColumn::make('group')->label('ჯგუფი')->searchable(),
                IconColumn::make('is_public')->label('საჯარო')->boolean(),
                TextColumn::make('updated_at')
                    ->label('განახლდა')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
