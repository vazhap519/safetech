<?php

namespace App\Filament\Resources\ProductFilters\Pages;

use App\Filament\Resources\ProductFilters\ProductFilterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductFilter extends CreateRecord
{
    protected static string $resource = ProductFilterResource::class;
}
