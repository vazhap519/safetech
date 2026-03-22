<?php

namespace App\Filament\Resources\FaqSections\Pages;

use App\Filament\Resources\FaqSections\FaqSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaqSections extends ListRecords
{
    protected static string $resource = FaqSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
