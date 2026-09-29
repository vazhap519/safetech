<?php

namespace App\Filament\Resources\QuoteCatalogItemResource\Pages;

use App\Filament\Resources\QuoteCatalogItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuoteCatalogItem extends EditRecord
{
    protected static string $resource = QuoteCatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->requiresConfirmation(),
        ];
    }
}
