<?php

namespace App\Filament\Resources\HomeCtaSections\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
class HomeCtaSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('cta_title')
            ->label('სათაური')->required(),
            
 TextInput::make('cta_title_hilight')->required()->label('სათაური 2 ნაწილი'),
 TextInput::make('cta_description')->required()->label('აღწერა'),
 TextInput::make('cta_phone_btn_number')->required()->label('ტელეფონის ნომერი'),
 TextInput::make('cta_phone_btn_text')->required()->label('ტელეფონის ნომრის ტექტი'),
 TextInput::make('cta_message_button_text')->required()->label('შეტყობინების გაგზავნის ღილაკის ტექსტი'),
            ]);
    }
}
