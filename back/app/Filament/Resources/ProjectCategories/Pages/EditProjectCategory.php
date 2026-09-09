<?php

namespace App\Filament\Resources\ProjectCategories\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectCategory extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
