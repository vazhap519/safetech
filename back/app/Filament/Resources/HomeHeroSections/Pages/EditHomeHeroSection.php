<?php

namespace App\Filament\Resources\HomeHeroSections\Pages;

use App\Filament\Resources\HomeHeroSections\HomeHeroSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeHeroSection extends EditRecord
{
    protected static string $resource = HomeHeroSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
