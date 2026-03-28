<?php

namespace App\Filament\Resources\HomeTestimonials\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HomeTestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')->default(true),

                Repeater::make('items')
                    ->label('Testimonials')
                    ->schema([
                        TextInput::make('name')->required(),
                        Textarea::make('text')->required(),

                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('avatars')
                            ->image()
                            ->label('ფოტო'),
                    ])
                    ->defaultItems(2),
            ]);
    }
}
