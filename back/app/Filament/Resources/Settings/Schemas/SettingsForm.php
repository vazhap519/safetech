<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class SettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('გაზიარება')->schema([
                    TextInput::make('share_title')->label('გაზიარების სათაური'),

                    Repeater::make('share_buttons')
                        ->label('გაზიარების ღილაკები')
                        ->schema([
                            Select::make('share_button_type')
                                ->label('პლატფორმა')
                                ->options([
                                    'facebook' => 'Facebook',
                                    'whatsapp' => 'WhatsApp',
                                    'telegram' => 'Telegram',
                                    'linkedin' => 'LinkedIn',
                                    'pinterest' => 'Pinterest',
                                    'twitter' => 'Twitter (X)',
                                    'link' => 'Copy Link',
                                ])
                                ->required()
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {

                                    $map = [
                                        'facebook' => [
                                            'name' => 'Facebook',
                                            'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                                            'color' => 'bg-blue-600',
                                            'icon' => 'FaFacebook',
                                        ],
                                        'whatsapp' => [
                                            'name' => 'WhatsApp',
                                            'url' => 'https://wa.me/?text={url}',
                                            'color' => 'bg-green-500',
                                            'icon' => 'FaWhatsapp',
                                        ],
                                        'telegram' => [
                                            'name' => 'Telegram',
                                            'url' => 'https://t.me/share/url?url={url}',
                                            'color' => 'bg-sky-500',
                                            'icon' => 'FaTelegram',
                                        ],
                                        'linkedin' => [
                                            'name' => 'LinkedIn',
                                            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                                            'color' => 'bg-blue-700',
                                            'icon' => 'FaLinkedin',
                                        ],
                                        'pinterest' => [
                                            'name' => 'Pinterest',
                                            'url' => 'https://pinterest.com/pin/create/button/?url={url}',
                                            'color' => 'bg-red-600',
                                            'icon' => 'FaPinterest',
                                        ],
                                        'twitter' => [
                                            'name' => 'Twitter',
                                            'url' => 'https://twitter.com/intent/tweet?url={url}',
                                            'color' => 'bg-black',
                                            'icon' => 'FaTwitter',
                                        ],
                                        'link' => [
                                            'name' => 'Copy Link',
                                            'url' => '{url}',
                                            'color' => 'bg-gray-600',
                                            'icon' => 'FaLink',
                                        ],
                                    ];

                                    if (isset($map[$state])) {
                                        $set('name', $map[$state]['name']);
                                        $set('url', $map[$state]['url']);
                                        $set('color', $map[$state]['color']);
                                        $set('icon', $map[$state]['icon']);
                                    }
                                }),

                            TextInput::make('name')->required(),
                            TextInput::make('url')->required()->columnSpanFull(),
                            TextInput::make('color')->required(),
                            Hidden::make('icon'),
                        ])
                        ->itemLabel(fn($state) => $state['name'] ?? 'Button')
                        ->reorderable()
                        ->collapsible()
                        ->columns(2),
                ]),

                Section::make('Footer')->schema([
                    TextInput::make('footer_copyright_text'),
                    TextInput::make('footer_brand_text'),

                    Repeater::make('footer_brand_soc')->schema([
                        Select::make('icon')->options([
                            'FaFacebook' => 'Facebook',
                            'FaInstagram' => 'Instagram',
                            'FaLinkedin' => 'LinkedIn',
                            'FaTwitter' => 'Twitter',
                            'FaYoutube' => 'YouTube',
                        ])->required(),

                        TextInput::make('url')->required(),
                        TextInput::make('text'),
                        ColorPicker::make('bg_color'),
                        ColorPicker::make('hover_color'),
                    ])->columnSpanFull(),
                ]),

                Section::make('Contact-page')->schema([

                    TextInput::make('contact_page_hero_title')->required(),
                    TextInput::make('contact_page_hero_text')->required(),



                ])
                    ->columns(2),

            ]);
    }
}
