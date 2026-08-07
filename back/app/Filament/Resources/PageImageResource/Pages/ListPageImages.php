<?php

namespace App\Filament\Resources\PageImageResource\Pages;

use App\Filament\Resources\PageImageResource;
use Filament\Resources\Pages\ListRecords;

class ListPageImages extends ListRecords
{
    protected static string $resource = PageImageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
