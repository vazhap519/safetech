<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\FaqResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaq extends CreateRecord
{
    use HasAiContentGenerator;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }
}
