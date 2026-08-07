<?php

namespace App\Filament\Resources\AboutPageResource\Pages;

use App\Filament\Resources\AboutPageResource;
use App\Filament\Support\AboutPageTranslationFields;
use Filament\Resources\Pages\EditRecord;

class EditAboutPageSetting extends EditRecord
{
    protected static string $resource = AboutPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return AboutPageTranslationFields::hydrate($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['key'] = 'translations';
        $data['group'] = $this->record->group ?: 'general';
        $data['is_public'] = (bool) $this->record->is_public;
        $data['value'] = AboutPageTranslationFields::mergeIntoValue(
            $data,
            is_array($this->record->value) ? $this->record->value : [],
        );

        unset($data['about_page_translations']);

        return $data;
    }
}
