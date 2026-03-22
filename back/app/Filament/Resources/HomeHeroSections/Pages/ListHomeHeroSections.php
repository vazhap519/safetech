<?php

namespace App\Filament\Resources\HomeHeroSections\Pages;

use App\Filament\Resources\HomeHeroSections\HomeHeroSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeHeroSections extends ListRecords
{
    protected static string $resource = HomeHeroSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
