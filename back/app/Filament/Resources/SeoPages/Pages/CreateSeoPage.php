<?php

namespace App\Filament\Resources\SeoPages\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\SeoPages\SeoPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeoPage extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = SeoPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
