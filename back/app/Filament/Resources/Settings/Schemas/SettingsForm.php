<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
            ->live() // 🔥 ეს საჭიროა
            ->afterStateUpdated(function ($state, callable $set) {

                $map = [
                    'facebook' => [
                        'name' => 'Facebook',
                        'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                        'color' => 'bg-blue-600',
                    ],
                    'whatsapp' => [
                        'name' => 'WhatsApp',
                        'url' => 'https://wa.me/?text={url}',
                        'color' => 'bg-green-500',
                    ],
                    'telegram' => [
                        'name' => 'Telegram',
                        'url' => 'https://t.me/share/url?url={url}',
                        'color' => 'bg-sky-500',
                    ],
                    'linkedin' => [
                        'name' => 'LinkedIn',
                        'url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                        'color' => 'bg-blue-700',
                    ],
                    'pinterest' => [
                        'name' => 'Pinterest',
                        'url' => 'https://pinterest.com/pin/create/button/?url={url}',
                        'color' => 'bg-pink-600',
                    ],
                    'twitter' => [
                        'name' => 'Twitter',
                        'url' => 'https://twitter.com/intent/tweet?url={url}',
                        'color' => 'bg-black',
                    ],
                    'link' => [
                        'name' => 'Copy Link',
                        'url' => '{url}',
                        'color' => 'bg-gray-600',
                    ],
                ];

                if (isset($map[$state])) {
                    $set('name', $map[$state]['name']);
                    $set('url', $map[$state]['url']);
                    $set('color', $map[$state]['color']);
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

    ])
    ->itemLabel(fn ($state) => $state['name'] ?? 'Button')
    ->reorderable()
    ->collapsible()
    ->columns(2),
                ]),
                Section::make('ფუთერი')
                    ->schema([

                        TextInput::make('footer_description')
                            ->label('ფუთერის აღწერა')
                            ->columnSpanFull(),

                        TextInput::make('footer_copyright')
                            ->label('ფუთერის კოპირაითი'),

                        /* ===== HEADERS ===== */
                        Repeater::make('footer_headers')
                            ->label('ფუთერის სათაურები')
                            ->schema([
                                TextInput::make('text')
                                    ->label('სათაური')
                                    ->required(),
                            ])
                            ->itemLabel(fn ($state) => $state['text'] ?? 'Header')
                            ->reorderable()
                            ->collapsible(),

                        /* ===== CONTACT INFO ===== */
                        Repeater::make('footer_contact_info')
                            ->label('საკონტაქტო ინფორმაცია')
                            ->schema([

                                Select::make('icon')
                                    ->label('აიქონი')
                                    ->options([
                                        'phone' => '📞 ტელეფონი',
                                        'email' => '✉️ იმეილი',
                                        'address' => '📍 მისამართი',
                                        'whatsapp' => '🟢 WhatsApp',
                                        'viber' => '🟣 Viber',
                                    ])
                                    ->required(),

                                TextInput::make('value')
                                    ->label('მნიშვნელობა')
                                    ->required(),

                            ])
                            ->itemLabel(fn ($state) => $state['icon'] ?? 'Item')
                            ->reorderable()
                            ->collapsible()
                            ->columns(2),

                        /* ===== SOCIALS ===== */
                        Repeater::make('footer_socials')
                            ->label('სოციალური ქსელები')
                            ->schema([

                                Select::make('icon')
                                    ->label('პლატფორმა')
                                    ->options([
                                        'facebook' => 'Facebook',
                                        'instagram' => 'Instagram',
                                        'linkedin' => 'LinkedIn',
                                        'youtube' => 'YouTube',
                                        'tiktok' => 'TikTok',
                                        'twitter' => 'Twitter (X)',
                                    ])
                                    ->required(),

                                TextInput::make('url')
                                    ->label('ლინკი')
                                    ->url()
                                    ->required(),

                            ])
                            ->itemLabel(fn ($state) => $state['icon'] ?? 'Social')
                            ->reorderable()
                            ->collapsible()
                            ->columns(2),


                    ])
            ]);
    }
}
