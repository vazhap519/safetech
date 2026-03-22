<?php

namespace App\Filament\Resources\ServiceSections\Pages;

use App\Filament\Resources\ServiceSections\ServiceSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceSection extends EditRecord
{
    protected static string $resource = ServiceSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
