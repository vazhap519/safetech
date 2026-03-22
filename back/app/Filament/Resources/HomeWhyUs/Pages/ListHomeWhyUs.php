<?php

namespace App\Filament\Resources\HomeWhyUs\Pages;

use App\Filament\Resources\HomeWhyUs\HomeWhyUsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeWhyUs extends ListRecords
{
    protected static string $resource = HomeWhyUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
