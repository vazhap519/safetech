<?php

namespace App\Filament\Resources\LocalServiceLandingResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\LocalServiceLandingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocalServiceLanding extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = LocalServiceLandingResource::class;

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
