<?php

namespace App\Filament\Resources\SeoPages\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\SeoPages\SeoPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSeoPage extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = SeoPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
