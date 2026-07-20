<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\SiteSetting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    protected static ?string $modelLabel = 'პარამეტრი';

    protected static ?string $pluralModelLabel = 'პარამეტრები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::System;

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
                    'integrations' => 'Analytics და საძიებო სისტემები',
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
                        ->label('ქვედა ზოლის სოციალური ბმულები')
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
                                ->helperText('ელფოსტისთვის მიუთითეთ მისამართი, WhatsApp-სთვის ნომერი, სხვა შემთხვევებში სრული URL ან დომენი.'),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->reorderable(),
                    TextInput::make('value.share_title')
                        ->label('გაზიარების სათაური'),
                    Repeater::make('value.share_buttons')
                        ->label('გაზიარების ღილაკები')
                        ->simple(
                            Select::make('type')->options([
                                'facebook' => 'Facebook',
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                                'linkedin' => 'LinkedIn',
                                'pinterest' => 'Pinterest',
                                'twitter' => 'X',
                                'link' => 'ბმულის კოპირება',
                            ]),
                        ),
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
                    SpatieMediaLibraryFileUpload::make('branding_logo')
                        ->label('ზედა მენიუს ლოგო')
                        ->collection('logo')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('branding_footer_logo')
                        ->label('ქვედა კოლონტიტულის ლოგო')
                        ->collection('footer_logo')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('branding_favicon')
                        ->label('საიტის ხატულა')
                        ->collection('favicon')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(2048),
                    SpatieMediaLibraryFileUpload::make('branding_default_image')
                        ->label('საერთო fallback სურათი')
                        ->collection('default_image')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->helperText('გამოიყენება ლოგოს გარდა იმ საერთო ვიზუალებში და preview-ებში, სადაც კონკრეტული სურათი არ არის შევსებული.'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'branding'),

            Section::make('საიტისა და LocalBusiness SEO')
                ->schema([
                    TextInput::make('value.site_name')->label('საიტის სახელი')->default('SafeTech'),
                    Textarea::make('value.site_description')->label('ორგანიზაციის აღწერა')->rows(3),
                    TextInput::make('value.city')->label('ქალაქი'),
                    TextInput::make('value.country')->label('ქვეყნის კოდი')->default('GE')->maxLength(2),
                    TextInput::make('value.postal_code')->label('საფოსტო ინდექსი'),
                    TextInput::make('value.lat')->label('განედი (latitude)')->numeric(),
                    TextInput::make('value.lng')->label('გრძედი (longitude)')->numeric(),
                    TextInput::make('value.open_time')->label('გახსნის დრო')->type('time'),
                    TextInput::make('value.close_time')->label('დახურვის დრო')->type('time'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'seo'),

            Section::make('ანალიტიკა, Pixel და ვერიფიკაცია')
                ->description('ID-ები საჯაროდ იტვირთება მხოლოდ მაშინ, როდესაც შესაბამისი ინტეგრაცია ჩართულია. GTM-ის გამოყენებისას GA4 tag თავად GTM-ში დაამატეთ, რათა page view ორჯერ არ ჩაითვალოს.')
                ->schema([
                    Toggle::make('value.marketing_enabled')
                        ->label('ანალიტიკისა და სარეკლამო კოდების ჩართვა')
                        ->default(false),
                    TextInput::make('value.google_tag_manager_id')
                        ->label('Google Tag Manager ID')
                        ->regex('/^GTM-[A-Z0-9]+$/i')
                        ->placeholder('GTM-XXXXXXX'),
                    TextInput::make('value.google_analytics_id')
                        ->label('Google Analytics 4 ID')
                        ->regex('/^G-[A-Z0-9]+$/i')
                        ->placeholder('G-XXXXXXXXXX'),
                    TextInput::make('value.meta_pixel_id')
                        ->label('Meta / Facebook Pixel ID')
                        ->regex('/^[0-9]{5,32}$/')
                        ->placeholder('123456789012345'),
                    TextInput::make('value.google_site_verification')
                        ->label('Google Search Console ვერიფიკაციის კოდი'),
                    TextInput::make('value.bing_site_verification')
                        ->label('Bing Webmaster Tools ვერიფიკაციის კოდი'),
                    TextInput::make('value.yandex_site_verification')
                        ->label('Yandex Webmaster ვერიფიკაციის კოდი'),
                    TextInput::make('value.indexnow_key')
                        ->label('IndexNow გასაღები')
                        ->helperText('გამოიყენება Bing/Yandex-ისთვის URL-ების სწრაფად გასაგზავნად.'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'integrations'),

            Section::make('ინტერფეისის თარგმანები')
                ->schema([
                    Repeater::make('value.entries')
                        ->label('თარგმანის ჩანაწერები')
                        ->schema([
                            TextInput::make('key')
                                ->label('გასაღები')
                                ->required()
                                ->helperText('მაგ: nav.home, meta.home.title, meta.home.description, services.hero.eyebrow, project.slug.card.title'),
                            TextInput::make('ka')
                                ->label('ქართული'),
                            TextInput::make('en')
                                ->label('ინგლისური'),
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
                ->label('საჯარო API-ში გამოჩენა')
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
