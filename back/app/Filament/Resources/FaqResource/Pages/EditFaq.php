<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\FaqResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make(),
        ];
    }
}
