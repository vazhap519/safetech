<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category')
                    ->schema([

                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $state) =>
                            $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->required(),

                        /* 🎨 COLOR */
                        ColorPicker::make('color')
                            ->label('Color')
                            ->default('#00C2A8'),

                        /* 🎯 ICON */
                        TextInput::make('icon')
                            ->label('Icon (emoji or class)')
                            ->placeholder('📷 or heroicon'),

                        /* 🔢 ORDER */
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),

                    ])
                    ->columns(2),
            ]);
    }
}
