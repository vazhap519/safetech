<?php

namespace App\Filament\Resources\AboutPageResource\Pages;

use App\Filament\Resources\AboutPageResource;
use App\Filament\Support\AboutPageTranslationFields;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditAboutPageSetting extends EditRecord
{
    protected static string $resource = AboutPageResource::class;

    public string $section = 'hero-story';

    public function mount(int|string $record, ?string $section = null): void
    {
        $this->section = AboutPageTranslationFields::normalizeSection($section);

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return array_map(
            fn (array $section): Action => Action::make("about_section_{$section['id']}")
                ->label($section['label'])
                ->color($section['id'] === $this->section ? 'primary' : 'gray')
                ->url(fn (): string => AboutPageResource::getUrl('edit', [
                    'record' => $this->getRecord(),
                    'section' => $section['id'],
                ])),
            AboutPageTranslationFields::navigation(),
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            AboutPageTranslationFields::sections($this->section),
        );
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
            $this->section,
        );

        unset($data['about_page_translations']);

        return $data;
    }
}
