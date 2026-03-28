<?php

namespace App\Filament\Resources\HomeTrusts\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HomeTrustForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')
                    ->label('აქტიურია')
                    ->default(true),

                SpatieMediaLibraryFileUpload::make('logos')
                    ->collection('logos')
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->label('ლოგოები'),
            ]);
    }
}
