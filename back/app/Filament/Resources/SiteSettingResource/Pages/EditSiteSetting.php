<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Filament\Support\ManagedPageTranslationFields;
use App\Support\SiteSettingValueNormalizer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('წაშლა')
                ->requiresConfirmation(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (is_string($data['key'] ?? null)) {
            $data['value'] = SiteSettingValueNormalizer::normalize(
                $data['key'],
                $data['value'] ?? [],
            );
        }

        return ManagedPageTranslationFields::hydrate($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = ManagedPageTranslationFields::dehydrate($data);

        if (is_string($data['key'] ?? null)) {
            $data['value'] = SiteSettingValueNormalizer::normalize(
                $data['key'],
                $data['value'] ?? [],
            );
        }

        return $data;
    }
}
