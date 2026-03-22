<?php

namespace App\Filament\Resources\HomeHowWorks\Pages;

use App\Filament\Resources\HomeHowWorks\HomeHowWorkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeHowWork extends EditRecord
{
    protected static string $resource = HomeHowWorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
