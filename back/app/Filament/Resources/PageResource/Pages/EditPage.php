<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
