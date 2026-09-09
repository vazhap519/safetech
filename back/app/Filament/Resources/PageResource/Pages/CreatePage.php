<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
