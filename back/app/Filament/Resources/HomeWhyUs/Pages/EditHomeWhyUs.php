<?php

namespace App\Filament\Resources\HomeWhyUs\Pages;

use App\Filament\Resources\HomeWhyUs\HomeWhyUsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeWhyUs extends EditRecord
{
    protected static string $resource = HomeWhyUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
