<?php

namespace App\Filament\Resources\CategoryForServices\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\CategoryForServices\CategoryForServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryForService extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = CategoryForServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
