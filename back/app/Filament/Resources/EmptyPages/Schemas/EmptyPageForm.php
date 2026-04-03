<?php

namespace App\Filament\Resources\EmptyPages\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmptyPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('empty_page_content')->label('ცარიელი გვერდის კონტენტი')->schema([
                    TextInput::make('title')->label('სათაური'),
                    TextInput::make('description')->label('აღწერა'),
                    TextInput::make('coming_soon')->label('მალე დავბრუნდებით ტექქსტი'),
                ]),
                Section::make('Social Links')
                    ->schema([

                        Repeater::make('socials')
                            ->label('Socials')
                            ->schema([

                                Select::make('icon')
                                    ->label('Icon')
                                    ->options([
                                        'facebook' => '🔵 Facebook',
                                        'instagram' => '📸 Instagram',
                                        'linkedin' => '💼 LinkedIn',
                                        'twitter' => '🐦 Twitter',
                                        'youtube' => '▶️ YouTube',
                                        'tiktok' => '🎵 TikTok', // ✅ დამატებული
                                    ])
                                    ->required(),

                                TextInput::make('url')
                                    ->label('URL')
                                    ->required(),

                            ])
                            ->columns(2)
                            ->addActionLabel('➕ დაამატე სოციალური')
                            ->reorderable()
                            ->collapsible()

                    ])

            ]);

    }
}
