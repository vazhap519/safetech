<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Support\CategoryFields;
use App\Filament\Support\CategorySeoFields;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CategoryFields::core(),

                ...CategorySeoFields::sections(),
            ]);
    }
}
