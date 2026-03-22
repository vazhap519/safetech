<?php

namespace App\Filament\Resources\ServiceSections\Pages;

use App\Filament\Resources\ServiceSections\ServiceSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceSections extends ListRecords
{
    protected static string $resource = ServiceSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
