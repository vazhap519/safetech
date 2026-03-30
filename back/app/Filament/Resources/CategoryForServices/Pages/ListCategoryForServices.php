<?php

namespace App\Filament\Resources\CategoryForServices\Pages;

use App\Filament\Resources\CategoryForServices\CategoryForServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryForServices extends ListRecords
{
    protected static string $resource = CategoryForServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
