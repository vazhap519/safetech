<?php

namespace App\Filament\Resources\CategoryForServices\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\CategoryForServices\CategoryForServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryForService extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = CategoryForServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make()
                ->label('წაშლა')
                ->requiresConfirmation(),
        ];
    }
}
