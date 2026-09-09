<?php

namespace App\Filament\Resources\LocalServiceLandingResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\LocalServiceLandingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLocalServiceLanding extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = LocalServiceLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
