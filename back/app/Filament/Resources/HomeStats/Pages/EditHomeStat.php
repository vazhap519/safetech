<?php

namespace App\Filament\Resources\HomeStats\Pages;

use App\Filament\Resources\HomeStats\HomeStatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeStat extends EditRecord
{
    protected static string $resource = HomeStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
