<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Filament\Support\AdminIconOptions;
use App\Filament\Support\ManagedPageTranslationFields;
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

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $modelLabel = 'Setting';

    protected static ?string $pluralModelLabel = 'Settings';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::System;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('key')
                ->label('Key')
                ->options([
                    'contact' => 'Contact',
                    'socials' => 'Social links',
                    'branding' => 'Branding',
                    'seo' => 'SEO',
                    'integrations' => 'Analytics and verification',
                    'translations' => 'Translations',
                ])
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Contact, socials, branding and translations are used directly on the frontend.'),
            Select::make('group')
                ->label('Group')
                ->options(['general' => 'General'])
                ->default('general')
                ->required(),

            Section::make('Contact details')
                ->schema([
                    TextInput::make('value.phone')
                        ->label('Primary phone')
                        ->helperText('Optional. If left empty, the first number from the list below becomes the primary phone.'),
                    Repeater::make('value.phones')
                        ->label('Additional phone numbers')
                        ->simple(
                            TextInput::make('value')
                                ->label('Phone number')
                                ->required(),
                        )
                        ->default([]),
                    TextInput::make('value.email')
                        ->label('Email')
                        ->email()
                        ->required(),
                    TextInput::make('value.lead_email')
                        ->label('Lead notification email')
                        ->email()
                        ->helperText('Lead form submissions are delivered here unless a production override is configured.')
                        ->default('safetechgeorgia@gmail.com'),
                    TextInput::make('value.whatsapp')
                        ->label('WhatsApp number')
                        ->helperText('Use the international number format, for example 995599123456.'),
                    TextInput::make('value.whatsapp_message')
                        ->label('WhatsApp default message'),
                    TextInput::make('value.hours')
                        ->label('Working hours'),
                    Textarea::make('value.address')
                        ->label('Address')
                        ->rows(2)
                        ->required(),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'contact'),

            Section::make('Footer social networks')
                ->schema([
                    Repeater::make('value.links')
                        ->label('Social links')
                        ->schema([
                            Select::make('network')
                                ->label('Network')
                                ->options(AdminIconOptions::socials())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->helperText('The matching social icon is rendered automatically on the frontend.'),
                            TextInput::make('label')
                                ->label('Label')
                                ->helperText('Optional custom label for the footer tooltip.'),
                            TextInput::make('href')
                                ->label('URL or value')
                                ->required()
                                ->helperText('Use an email address for Email, a phone number for WhatsApp, or a full URL/domain for the rest.'),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->reorderable(),
                    TextInput::make('value.share_title')
                        ->label('Share title'),
                    Repeater::make('value.share_buttons')
                        ->label('Share buttons')
                        ->simple(
                            Select::make('type')->options([
                                'facebook' => 'Facebook',
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                                'linkedin' => 'LinkedIn',
                                'pinterest' => 'Pinterest',
                                'twitter' => 'X',
                                'link' => 'Copy link',
                            ]),
                        ),
                ])
                ->visible(fn (Get $get): bool => $get('key') === 'socials'),

            Section::make('Branding')
                ->schema([
                    TextInput::make('value.site_name')
                        ->label('Site name')
                        ->required(),
                    TextInput::make('value.tagline')
                        ->label('Tagline')
                        ->helperText('Displayed as footer copy.'),
                    SpatieMediaLibraryFileUpload::make('branding_logo')
                        ->label('Header logo')
                        ->collection('logo')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('branding_footer_logo')
                        ->label('Footer logo')
                        ->collection('footer_logo')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('branding_favicon')
                        ->label('Favicon')
                        ->collection('favicon')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(2048),
                    SpatieMediaLibraryFileUpload::make('branding_default_image')
                        ->label('Default social/image fallback')
                        ->collection('default_image')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->helperText('Used when a page or content item does not define its own image.'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'branding'),

            Section::make('Site and LocalBusiness SEO')
                ->schema([
                    TextInput::make('value.site_name')->label('Site name')->default('SafeTech'),
                    Textarea::make('value.site_description')->label('Organization description')->rows(3),
                    TextInput::make('value.city')->label('City'),
                    TextInput::make('value.country')->label('Country code')->default('GE')->maxLength(2),
                    TextInput::make('value.postal_code')->label('Postal code'),
                    TextInput::make('value.lat')->label('Latitude')->numeric(),
                    TextInput::make('value.lng')->label('Longitude')->numeric(),
                    TextInput::make('value.open_time')->label('Open time')->type('time'),
                    TextInput::make('value.close_time')->label('Close time')->type('time'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'seo'),

            Section::make('Analytics, pixels and verification')
                ->description('IDs are only exposed publicly when marketing integrations are enabled.')
                ->schema([
                    Toggle::make('value.marketing_enabled')
                        ->label('Enable analytics and marketing scripts')
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
                        ->label('Google Search Console verification'),
                    TextInput::make('value.bing_site_verification')
                        ->label('Bing Webmaster Tools verification'),
                    TextInput::make('value.yandex_site_verification')
                        ->label('Yandex Webmaster verification'),
                    TextInput::make('value.indexnow_key')
                        ->label('IndexNow key')
                        ->helperText('Used for faster URL notifications to Bing and Yandex.'),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'integrations'),

            ...ManagedPageTranslationFields::sections(),

            Section::make('Additional translation entries')
                ->schema([
                    Repeater::make('value.entries')
                        ->label('Translation entries')
                        ->schema([
                            TextInput::make('key')
                                ->label('Key')
                                ->required()
                                ->helperText('Example: nav.home, blog.title, services.hero.eyebrow, project.slug.card.title'),
                            TextInput::make('ka')->label('Georgian'),
                            TextInput::make('en')->label('English'),
                            TextInput::make('ru')->label('Russian'),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->helperText('Use this repeater for any page or component copy that does not have a dedicated section above.'),
                ])
                ->visible(fn (Get $get): bool => $get('key') === 'translations'),

            Toggle::make('is_public')
                ->label('Expose through the public API')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Key')->searchable()->sortable(),
                TextColumn::make('group')->label('Group')->searchable(),
                IconColumn::make('is_public')->label('Public')->boolean(),
                TextColumn::make('updated_at')
                    ->label('Updated')
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
