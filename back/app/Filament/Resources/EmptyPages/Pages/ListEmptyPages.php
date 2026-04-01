<?php

namespace App\Filament\Resources\EmptyPages\Pages;

use App\Filament\Resources\EmptyPages\EmptyPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmptyPages extends ListRecords
{
    protected static string $resource = EmptyPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
