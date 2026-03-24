<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ColorPicker;
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
                    $set('icon', $map[$state]['icon']); // 🔥 ახალი
                }
            }),

        TextInput::make('name')
            ->label('სახელი')
            ->required(),

        TextInput::make('url')
            ->label('Share URL ({url})')
            ->required()
            ->columnSpanFull(),

        TextInput::make('color')
            ->label('Tailwind Color')
            ->required(),

        Hidden::make('icon'), // 🔥 ეს ინახავს icon-ს
    ])
    ->itemLabel(fn ($state) => $state['name'] ?? 'Button')
    ->reorderable()
    ->collapsible()
    ->columns(2),
                ]),


                Section::make('Footer')->schema([
                    TextInput::make('footer_copyright_text')->label('კოპირაითი'),
                    TextInput::make('footer_brand_text')->label('ფუთერში კომპანიის მოკლე აღწერა'),
                    Repeater::make('footer_brand_soc')->schema([
                        Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'FaFacebook' => 'Facebook',
                                'FaInstagram' => 'Instagram',
                                'FaLinkedin' => 'LinkedIn',
                                'FaTwitter' => 'Twitter',
                                'FaYoutube' => 'YouTube',
                            ])
                            ->searchable()
                            ->required(),

                        TextInput::make('url')
                            ->label('Link')
                            ->url()
                            ->required(),

                        TextInput::make('text')
                            ->label('Tooltip / Text'),

                        ColorPicker::make('bg_color')
                            ->label('Background'),

                        ColorPicker::make('hover_color')
                            ->label('Hover Color'),
                    ])->label('ფუთერის სოციალური ქსელები')
                    ->reorderable()
                    ->collapsible()
                    ->columnSpanFull(),
                    Repeater::make('footer_headers')
                        ->schema([
                            Select::make('key')
                                ->options([
                                    'services' => 'Services',
                                    'company' => 'Company',
                                    'contact' => 'Contact',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {

                                    $map = [
                                        'services' => 'სერვისები',
                                        'company' => 'კომპანია',
                                        'contact' => 'კონტაქტი',
                                    ];

                                    if (isset($map[$state])) {
                                        $set('title', $map[$state]);
                                    }
                                }),

                            TextInput::make('title')
                                ->label('ფუთერის სათაური')
                                ->required(),
                        ])
                        ->reorderable()
                        ->collapsible()
                        ->columnSpanFull(),
                    Repeater::make('footer_contact_area')
                        ->label('Contact Info')
                        ->schema([

                            Select::make('type')
                                ->label('Type')
                                ->options([
                                    'phone' => 'Phone',
                                    'email' => 'Email',
                                    'address' => 'Address',
                                    'whatsapp' => 'WhatsApp',
                                    'viber' => 'Viber',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {

                                    $map = [
                                        'phone' => [
                                            'icon' => 'FaPhone',
                                            'prefix' => 'tel:',
                                        ],
                                        'email' => [
                                            'icon' => 'FaEnvelope',
                                            'prefix' => 'mailto:',
                                        ],
                                        'address' => [
                                            'icon' => 'FaMapMarkerAlt',
                                            'prefix' => '',
                                        ],
                                        'whatsapp' => [
                                            'icon' => 'FaWhatsapp',
                                            'prefix' => 'https://wa.me/',
                                        ],
                                        'viber' => [
                                            'icon' => 'FaViber',
                                            'prefix' => 'viber://chat?number=',
                                        ],
                                    ];

                                    if (isset($map[$state])) {
                                        $set('icon', $map[$state]['icon']);
                                        $set('prefix', $map[$state]['prefix']);
                                    }
                                }),

                            TextInput::make('label')
                                ->label('Label (optional)'),

                            TextInput::make('value')
                                ->label('Value')
                                ->required(),

                            Hidden::make('icon'),
                            Hidden::make('prefix'),

                        ])
                        ->columns(2)
                        ->reorderable()
                        ->collapsible()


                ])
            ]);
    }
}
