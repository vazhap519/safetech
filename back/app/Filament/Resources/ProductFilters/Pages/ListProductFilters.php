<?php

namespace App\Filament\Resources\ProductFilters\Pages;

use App\Filament\Resources\ProductFilters\ProductFilterResource;
use Filament\Resources\Pages\ListRecords;

class ListProductFilters extends ListRecords
{
    protected static string $resource = ProductFilterResource::class;
}
