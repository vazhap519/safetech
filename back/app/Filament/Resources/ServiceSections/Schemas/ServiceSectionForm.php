<?php

namespace App\Filament\Resources\ServiceSections\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
class ServiceSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('მთავარ გვერძე სექციების სექცია')->schema([
    TextInput::make('service_section_title')->label('სათაური')->required(),
TextInput::make('service_section_description')->label('აღწერა')->required(),
            ]),
            ]);
    }
}
