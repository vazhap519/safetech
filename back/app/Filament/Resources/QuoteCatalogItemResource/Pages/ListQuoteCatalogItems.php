<?php

namespace App\Filament\Resources\QuoteCatalogItemResource\Pages;

use App\Filament\Resources\QuoteCatalogItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuoteCatalogItems extends ListRecords
{
    protected static string $resource = QuoteCatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('ახალი პოზიცია'),
        ];
    }
}
