<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
