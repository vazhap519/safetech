<?php

namespace App\Filament\Resources\FaqSections\Pages;

use App\Filament\Resources\FaqSections\FaqSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaqSection extends EditRecord
{
    protected static string $resource = FaqSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
