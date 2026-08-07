<?php

namespace App\Filament\Resources\AboutPageResource\Pages;

use App\Filament\Resources\AboutPageResource;
use App\Filament\Support\AboutPageTranslationFields;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateAboutPageSetting extends CreateRecord
{
    protected static string $resource = AboutPageResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            AboutPageTranslationFields::sections('hero-story'),
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = 'translations';
        $data['group'] = 'general';
        $data['is_public'] = true;
        $data['value'] = AboutPageTranslationFields::mergeIntoValue(
            $data,
            [],
            'hero-story',
        );

        unset($data['about_page_translations']);

        return $data;
    }
}
