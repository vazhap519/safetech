<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Filament\Support\ManagedPageTranslationFields;
use App\Support\SiteSettingValueNormalizer;
use Filament\Resources\Pages\CreateRecord;

class CreateSiteSetting extends CreateRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
