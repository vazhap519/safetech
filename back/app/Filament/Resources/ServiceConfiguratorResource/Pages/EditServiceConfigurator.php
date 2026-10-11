<?php

namespace App\Filament\Resources\ServiceConfiguratorResource\Pages;

use App\Filament\Concerns\HasAiContentGenerator;
use App\Filament\Resources\ServiceConfiguratorResource;
use Filament\Resources\Pages\EditRecord;

class EditServiceConfigurator extends EditRecord
{
    use HasAiContentGenerator;

    protected static string $resource = ServiceConfiguratorResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['lead_form']) && is_array($data['lead_form'])) {
            $existing = $this->record->lead_form ?? [];
            $data['lead_form'] = array_replace(
                is_array($existing) ? $existing : [],
                $data['lead_form'],
            );
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->aiContentAction(),
        ];
    }

    protected function aiContentProfile(): string
    {
        return 'service';
    }
}
