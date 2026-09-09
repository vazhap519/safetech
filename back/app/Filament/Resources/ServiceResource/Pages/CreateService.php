<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
