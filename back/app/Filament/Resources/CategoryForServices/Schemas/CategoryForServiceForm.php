<?php

namespace App\Filament\Resources\CategoryForServices\Schemas;

use App\Filament\Support\CategoryFields;
use App\Filament\Support\CategorySeoFields;
use Filament\Schemas\Schema;

class CategoryForServiceForm
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
