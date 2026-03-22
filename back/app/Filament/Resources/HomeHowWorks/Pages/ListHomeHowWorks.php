<?php

namespace App\Filament\Resources\HomeHowWorks\Pages;

use App\Filament\Resources\HomeHowWorks\HomeHowWorkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeHowWorks extends ListRecords
{
    protected static string $resource = HomeHowWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
