<?php

namespace App\Filament\Resources\EmptyPages\Pages;

use App\Filament\Resources\EmptyPages\EmptyPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmptyPage extends EditRecord
{
    protected static string $resource = EmptyPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
