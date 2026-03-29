<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ColorPicker;

class SettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | ✅ SHARE (FIXED - CLEAN)
                |--------------------------------------------------------------------------
                */
                Section::make('გაზიარება')->schema([

                    TextInput::make('share_title')
                        ->label('გაზიარების სათაური'),

                    Repeater::make('share_buttons')
                        ->label('გაზიარების ღილაკები')
                        ->schema([

                            Select::make('type') // ✅ renamed
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
                                ->searchable(),

                        ])
                        ->itemLabel(fn($state) => $state['type'] ?? 'Button')
                        ->reorderable()
                        ->collapsible()
                        ->columns(1),
                ]),

                /*
                |--------------------------------------------------------------------------
                | FOOTER (UNCHANGED)
                |--------------------------------------------------------------------------
                */
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

                    Section::make('📍 SEO / Local Business')
                        ->schema([
                            TextInput::make('phone')->label('Phone'),
                            TextInput::make('email')->label('Email'),

                            TextInput::make('address')->label('Address'),
                            TextInput::make('city')->label('City'),
                            TextInput::make('country')->label('Country'),

                            TextInput::make('lat')->label('Latitude'),
                            TextInput::make('lng')->label('Longitude'),

                            TextInput::make('open_time')->label('Open Time'),
                            TextInput::make('close_time')->label('Close Time'),

                            TextInput::make('facebook')->label('Facebook URL'),
                            TextInput::make('instagram')->label('Instagram URL'),
                            TextInput::make('linkedin')->label('LinkedIn URL'),
                        ])
                        ->columns(2),
                ]),
            ]);
    }
}
