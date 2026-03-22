<?php

namespace App\Filament\Resources\HomeCtaSections\Pages;

use App\Filament\Resources\HomeCtaSections\HomeCtaSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeCtaSection extends EditRecord
{
    protected static string $resource = HomeCtaSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
