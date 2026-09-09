<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
            DeleteAction::make()
                ->label('წაშლა')
                ->requiresConfirmation(),
        ];
    }
}
