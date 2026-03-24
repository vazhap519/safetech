<?php

namespace App\Filament\Resources\Abouts\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\RichEditor;

class AboutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
              Section::make('AboutHero')->schema([
                  TextInput::make('hero_title')
                      ->label('ჰერო სექციის სათაური')
                      ->required()
                      ->maxLength(255),
RichEditor::make('hero_description')
    ->label('ჰერო სექციის მოკლე აღწერა')
    ->required(),
TextInput::make('hero_badge')->label('ჰერო სექციის badge ტექსტი')->required()->maxLength(255),
Repeater::make('hero_trust_list')->schema([
    TextInput::make('hero_trust_list_title'),

])


              ])->label('ჩვენს შესახებ ჰერო სექცია'),
                Section::make('AboutStory')->schema([
                    TextInput::make('story_title')
                    ->label('სათაური')
                    ->required()
                    ->maxLength(255),
                    TextInput::make('story_title_description')
                    ->label('მოკლე აღწერა')
                    ->required()
                    ->maxLength(255),
                    RichEditor::make('story_content')
                        ->label('კონტანტი')
                    ->required(),
                    Repeater::make('story_stats')->schema([
                        TextInput::make('story_stats_label')->label('სათაური'),
                        TextInput::make('story_stats_numbers')->label('ციფრები'),
                    ])->label('სტატისტიკა')
                    ,
                ])->label('ჩვენს შესახებ ისტორიის სექცია'),
                Section::make('AboutWhy')->schema([
                    TextInput::make('why_us_title')
                        ->label('სათაური')
                    ->required()
                    ->maxLength(255),
                    TextInput::make('why_us_title_description')
                        ->label('მოკლე აღწერა')
                    ->required()
                    ->maxLength(255),
                    Repeater::make('why_us_content')->schema([
                        TextInput::make('title')
                            ->required()
                        ->maxLength(255)
                        ->label('სათაური'),
                        TextInput::make('desc')
                            ->required()
                        ->maxLength(255)
                        ->label('აღწერა'),
                        Select::make('why_us_icons')->options([
                            // ⚡ ზოგადი
                            'FaBolt' => '⚡ სწრაფი რეაგირება',
                            'FaTools' => '🛠 ტექნიკური სამუშაოები',
                            'FaCogs' => '⚙️ კონფიგურაცია',

                            // 💻 IT
                            'FaLaptop' => '💻 კომპიუტერები',
                            'FaDesktop' => '🖥 სამუშაო სადგური',
                            'FaServer' => '🗄 სერვერები',
                            'FaNetworkWired' => '🌐 ქსელები',

                            // 📡 ინტერნეტი / ქსელი
                            'FaWifi' => '📶 WiFi',
                            'FaRouter' => '📡 Router',

                            // 🎥 უსაფრთხოება / კამერები
                            'FaVideo' => '🎥 CCTV კამერები',
                            'FaCamera' => '📷 კამერები',
                            'FaEye' => '👁 მონიტორინგი',

                            // 🔒 უსაფრთხოება
                            'FaLock' => '🔒 უსაფრთხოება',
                            'FaShieldAlt' => '🛡 დაცვა',

                            // 🏢 დომოფონი / Access
                            'FaDoorOpen' => '🚪 დომოფონი / კარი',
                            'FaBell' => '🔔 ზარი / დომოფონი',

                            // 📞 კომუნიკაცია
                            'FaPhone' => '📞 ტელეფონი',
                            'FaHeadset' => '🎧 მხარდაჭერა',

                            // 🎯 ბიზნეს
                            'FaBullseye' => '🎯 მიზანი',
                            'FaUsers' => '👥 მომხმარებლები',

                            // 📍 ლოკაცია
                            'FaMapMarkerAlt' => '📍 ლოკაცია',
                        ])
                    ])->label('ჩვენს შესახებ რატომ ჩვენ კონტენტი'),
                ])->label('ჩვენს შეახებ რატომ ჩვენ სექცია'),
                Section::make('AboutCta')->schema([
                    TextInput::make('cta_title')
                        ->required()
                    ->maxLength(255)
                    ->label('სათაური'),
                    TextInput::make('cta_title_description')
                    ->label('მოკლე აღწერა')
                    ->required()
                    ->maxLength(255),
                    Repeater::make('cta_trust')->schema([
                        TextInput::make('cta_trust_title')->label('სათაური')->required()->maxLength(255),
                    ]),
                    TextInput::make('cta_phone')
                        ->label('ტელეფონის ნომერი')
                        ->tel()
                        ->required()
                        ->rule('regex:/^\+?995\d{9}$/')
                        ->placeholder('+995599000000')
                ])->label('ჩვენს შესახებ კონტაქტი'),
SpatieMediaLibraryFileUpload::make('About_hero_image')
    ->collection('hero')
    ->image()
    ->imageEditor()
    ->required(),
            ]);
    }
}
