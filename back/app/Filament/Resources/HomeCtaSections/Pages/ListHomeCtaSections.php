<?php

namespace App\Filament\Resources\HomeCtaSections\Pages;

use App\Filament\Resources\HomeCtaSections\HomeCtaSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeCtaSections extends ListRecords
{
    protected static string $resource = HomeCtaSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
