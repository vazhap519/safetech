<?php

namespace App\Filament\Resources\ContactSections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact-page')->schema([


                    // 🔵 HERO
                    TextInput::make('contact_page_hero_title')
                        ->label('ჰედერის სათაური')
                        ->required(),

                    TextInput::make('contact_page_hero_text')
                        ->label('ჰედერის ტექსტი')
                        ->required(),

                    // 📞 PHONE
                    TextInput::make('contact_page_number')
                        ->label('ტელეფონი')
                        ->tel(),

                    // 🟡 WHY SECTION
                    TextInput::make('contact_page_why_title')
                        ->label('რატომ ჩვენ სათაური'),

                    Repeater::make('contact_page_why_text')
                        ->label('რატომ ჩვენ ტექსტები')
                        ->schema([
                            TextInput::make('text')->label('ტექსტი'),
                        ]),

                    // ℹ️ INFO TITLE
                    TextInput::make('contact_page_info_title')
                        ->label('ინფოს სათაური'),

                    // 📲 CONTACT INFO
                    TextInput::make('contact_page_whatsapp')
                        ->label('WhatsApp'),

                    TextInput::make('contact_page_viber')
                        ->label('Viber'),

                    TextInput::make('contact_page_email')
                        ->label('Email')
                        ->email(),

                    TextInput::make('contact_page_address')
                        ->label('მისამართი'),

                ])
                    ->columns(2)->label('კონტაქტის გვერდი'),
            ]);
    }
}
