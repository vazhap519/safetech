<?php

namespace App\Filament\Resources\ProjectCategories\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectCategory extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
