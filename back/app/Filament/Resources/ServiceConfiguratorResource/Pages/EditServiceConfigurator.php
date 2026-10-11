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
            $data['lead_form'] = $this->mergePreservingUnmanagedSettings(
                is_array($existing) ? $existing : [],
                $data['lead_form'],
            );;
        }

        return $data;
    }

    /**
     * Preserve unmanaged associative settings while replacing admin-managed lists
     * wholesale, including intentionally emptied repeaters.
     *
     * @param array<string, mixed> $existing
     * @param array<string, mixed> $incoming
     * @return array<string, mixed>
     */
    private function mergePreservingUnmanagedSettings(array $existing, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (
                isset($existing[$key])
                && is_array($existing[$key])
                && is_array($value)
                && ! array_is_list($existing[$key])
                && ! array_is_list($value)
            ) {
                $existing[$key] = $this->mergePreservingUnmanagedSettings($existing[$key], $value);
            } else {
                $existing[$key] = $value;
            }
        }

        return $existing;
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
