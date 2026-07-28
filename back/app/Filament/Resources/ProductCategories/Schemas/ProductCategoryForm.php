<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use App\Filament\Support\CategoryFields;
use App\Filament\Support\CategorySeoFields;
use Filament\Schemas\Schema;

class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            CategoryFields::core(withAppearance: true),
            ...CategorySeoFields::sections(),
        ]);
    }
}
