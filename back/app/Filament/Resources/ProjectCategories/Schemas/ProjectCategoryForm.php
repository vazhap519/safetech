<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Support\CategoryFields;
use App\Filament\Support\CategorySeoFields;
use Filament\Schemas\Schema;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CategoryFields::core(withAppearance: true),

                ...CategorySeoFields::sections(),
            ]);
    }
}
