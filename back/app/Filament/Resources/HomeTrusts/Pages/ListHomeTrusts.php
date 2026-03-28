<?php

namespace App\Filament\Resources\HomeTrusts\Pages;

use App\Filament\Resources\HomeTrusts\HomeTrustResource;
use App\Models\HomeTrust;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeTrusts extends ListRecords
{
    protected static string $resource = HomeTrustResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => HomeTrust::count() === 0),
        ];
    }
}
