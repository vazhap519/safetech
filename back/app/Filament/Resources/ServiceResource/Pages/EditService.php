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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['lead_form']) && is_array($data['lead_form'])) {
            $existing = $this->record->lead_form ?? [];
            $data['lead_form'] = array_replace($existing, $data['lead_form']);
        }

        return $data;
    }

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
