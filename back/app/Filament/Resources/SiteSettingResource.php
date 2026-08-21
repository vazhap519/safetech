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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
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

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $modelLabel = 'Setting';

    protected static ?string $pluralModelLabel = 'Site settings';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::System;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('key')
                ->label('Key')
                ->options([
                    'contact' => 'Contact',
                    'socials' => 'Social media',
                    'branding' => 'Branding',
                    'seo' => 'SEO',
                    'integrations' => 'Analytics and verification',
                    'translations' => 'Translations',
                ])
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Contact, social media, branding and translations are used directly on the frontend.'),
            Select::make('group')
                ->label('Group')
                ->options(['general' => 'General'])
                ->default('general')
                ->required(),

            Section::make('Contact details')
                ->description('WhatsApp settings control the floating contact button and the WhatsApp link in the footer.')
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
                    Toggle::make('value.whatsapp_enabled')
                        ->label('Show WhatsApp contact links')
                        ->helperText('Controls both the floating WhatsApp button and the footer WhatsApp icon.')
                        ->default(true)
                        ->columnSpanFull(),
                    TextInput::make('value.whatsapp')
                        ->label('WhatsApp number')
                        ->tel()
                        ->placeholder('+995 599 123 456')
                        ->helperText('Use an international number. Spaces and the + sign are accepted.'),
                    Textarea::make('value.whatsapp_message')
                        ->label('Pre-filled WhatsApp message')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Visitors can edit this message before sending it.'),
                    TextInput::make('value.hours')
                        ->label('Working hours'),
                    Textarea::make('value.address')
                        ->label('Address')
                        ->rows(2)
                        ->required(),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => $get('key') === 'contact'),

            Section::make('Social profiles')
                ->description('Add only the public profiles you want to show. Cards stay collapsed to keep this page short. Remove a card and click Save to persist the deletion.')
                ->columnSpanFull()
                ->schema([
                    Hidden::make('value.links_managed')
                        ->default(true),
                    Repeater::make('value.links')
                        ->label('Profiles')
                        ->schema([
                            ToggleButtons::make('network')
                                ->label('Choose a network')
                                ->options(AdminIconOptions::socials())
                                ->icons(AdminIconOptions::socialIcons())
                                ->colors(AdminIconOptions::socialColors())
                                ->tooltips(AdminIconOptions::socials())
                                ->hiddenButtonLabels()
                                ->columns(['default' => 3, 'sm' => 4, 'lg' => 6])
                                ->required()
                                ->helperText('Select the icon visitors should see for this profile.')
                                ->columnSpanFull(),
                            TextInput::make('href')
                                ->label('Profile URL / value')
                                ->required()
                                ->placeholder('https://facebook.com/...')
                                ->helperText('Use a full profile URL. Email, Viber and WhatsApp may use their direct value.')
                                ->columnSpan(['default' => 1, 'md' => 2]),
                            Toggle::make('enabled')
                                ->label('Show')
                                ->default(true)
                                ->columnSpan(['default' => 1, 'md' => 1]),
                            Toggle::make('open_in_new_tab')
                                ->label('Open in new tab')
                                ->default(true)
                                ->columnSpan(['default' => 1, 'md' => 1]),
                        ])
                        ->columns(['default' => 1, 'md' => 4])
                        ->default([])
                        ->collapsible()
                        ->collapsed()
                        ->reorderable()
                        ->addActionLabel('Add social profile')
                        ->columnSpanFull()
                        ->itemLabel(fn (array $state): ?string => AdminIconOptions::socials()[$state['network'] ?? ''] ?? 'Social profile'),
                ])
                ->visible(fn (Get $get): bool => $get('key') === 'socials'),

            Section::make('Sharing buttons')
                ->description('Choose the share actions that appear on service and project pages. Buttons are collapsed by default to keep the editor compact.')
                ->schema([
                    Toggle::make('value.share_enabled')
                        ->label('Enable sharing')
                        ->default(true),
                    Toggle::make('value.share_on_services')
                        ->label('Service pages')
                        ->default(true),
                    Toggle::make('value.share_on_projects')
                        ->label('Project pages')
                        ->default(true),
                    TextInput::make('value.share_title_ka')
                        ->label('Heading — KA')
                        ->default('გაზიარება'),
                    TextInput::make('value.share_title_en')
                        ->label('Heading — EN')
                        ->default('Share'),
                    TextInput::make('value.share_title_ru')
                        ->label('Heading — RU')
                        ->default('Поделиться'),
                    Repeater::make('value.share_buttons')
                        ->label('Buttons')
                        ->schema([
                            ToggleButtons::make('type')
                                ->label('Choose a sharing action')
                                ->options(AdminIconOptions::shareNetworks())
                                ->icons(AdminIconOptions::shareIcons())
                                ->colors(AdminIconOptions::shareColors())
                                ->tooltips(AdminIconOptions::shareNetworks())
                                ->hiddenButtonLabels()
                                ->columns(['default' => 3, 'sm' => 4, 'lg' => 5])
                                ->required()
                                ->helperText('Select the button visitors can use to share the current page.')
                                ->columnSpanFull(),
                            Toggle::make('enabled')
                                ->label('Show')
                                ->default(true)
                                ->columnSpanFull(),
                        ])
                        ->columns(['default' => 1, 'md' => 4])
                        ->default([])
                        ->collapsible()
                        ->collapsed()
                        ->reorderable()
                        ->addActionLabel('Add sharing button')
                        ->columnSpanFull()
                        ->itemLabel(fn (array $state): ?string => AdminIconOptions::shareNetworks()[$state['type'] ?? ''] ?? 'Share button'),
                ])
                ->columns(['default' => 1, 'lg' => 3])
                ->columnSpanFull()
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
                    TagsInput::make('value.default_keywords')
                        ->label('Default SEO keywords')
                        ->helperText('Used as a site-wide fallback when a page or product does not define its own keywords.'),
                    Toggle::make('value.robots_index')
                        ->label('Allow search engines to index the site')
                        ->default(true),
                    Toggle::make('value.robots_follow')
                        ->label('Allow search engines to follow links')
                        ->default(true),
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
                    TextInput::make('value.google_review_url')
                        ->label('Google Business reviews URL')
                        ->url()
                        ->helperText('Optional public Google Business Profile reviews link, shown next to verified client feedback.'),
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
