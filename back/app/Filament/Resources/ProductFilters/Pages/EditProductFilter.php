<?php

namespace App\Filament\Resources\ProductFilters\Pages;

use App\Filament\Resources\ProductFilters\ProductFilterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductFilter extends EditRecord
{
    protected static string $resource = ProductFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
