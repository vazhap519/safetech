<?php

namespace App\Filament\Resources\HomeTrusts\Pages;

use App\Filament\Resources\HomeTrusts\HomeTrustResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeTrust extends EditRecord
{
    protected static string $resource = HomeTrustResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
