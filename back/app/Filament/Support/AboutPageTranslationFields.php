<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

final class AboutPageTranslationFields
{
    /** @return array<int, Section> */
    public static function sections(): array
    {
        return array_map(
            fn (array $section): Section => Section::make($section['label'])
                ->description($section['description'] ?? null)
                ->schema(self::componentsFor($section['fields']))
                ->columns(3)
                ->collapsible(),
            self::definitions(),
        );
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function hydrate(array $data): array
    {
        $entries = data_get($data, 'value.entries', []);
        $entryMap = [];

        foreach ($entries as $entry) {
            if (! is_array($entry) || blank($entry['key'] ?? null)) {
                continue;
            }

            $entryMap[(string) $entry['key']] = [
                'ka' => trim((string) ($entry['ka'] ?? '')),
                'en' => trim((string) ($entry['en'] ?? '')),
                'ru' => trim((string) ($entry['ru'] ?? '')),
            ];
        }

        $managed = [];

        foreach (self::fieldIndex() as $id => $field) {
            $managed[$id] = $entryMap[$field['key']] ?? ['ka' => '', 'en' => '', 'ru' => ''];
        }

        $data['about_page_translations'] = $managed;

        return $data;
    }

    /** @param array<string, mixed> $formData
     * @param array<string, mixed> $existingValue
     * @return array<string, mixed>
     */
    public static function mergeIntoValue(array $formData, array $existingValue): array
    {
        $existingEntries = collect(data_get($existingValue, 'entries', []))
            ->filter(fn ($entry): bool => is_array($entry) && filled($entry['key'] ?? null))
            ->reject(fn (array $entry): bool => in_array((string) $entry['key'], self::managedKeys(), true))
            ->values()
            ->all();

        $managedEntries = collect(self::fieldIndex())
            ->map(function (array $field, string $id) use ($formData): ?array {
                $values = data_get($formData, "about_page_translations.{$id}", []);

                $entry = [
                    'key' => $field['key'],
                    'ka' => trim((string) ($values['ka'] ?? '')),
                    'en' => trim((string) ($values['en'] ?? '')),
                    'ru' => trim((string) ($values['ru'] ?? '')),
                ];

                return ($entry['ka'] !== '' || $entry['en'] !== '' || $entry['ru'] !== '')
                    ? $entry
                    : null;
            })
            ->filter()
            ->values()
            ->all();

        data_set($existingValue, 'entries', [...$existingEntries, ...$managedEntries]);

        return $existingValue;
    }

    /** @return array<int, string> */
    private static function managedKeys(): array
    {
        return array_values(array_map(
            fn (array $field): string => $field['key'],
            self::flatFields(),
        ));
    }

    /** @param array<int, array<string, mixed>> $fields
     * @return array<int, TextInput|Textarea>
     */
    private static function componentsFor(array $fields): array
    {
        $components = [];

        foreach ($fields as $field) {
            foreach (['ka' => 'KA', 'en' => 'EN', 'ru' => 'RU'] as $locale => $localeLabel) {
                $name = "about_page_translations.{$field['id']}.{$locale}";
                $label = "{$field['label']} ({$localeLabel})";

                $components[] = ($field['type'] ?? 'text') === 'textarea'
                    ? Textarea::make($name)
                        ->label($label)
                        ->rows((int) ($field['rows'] ?? 3))
                    : TextInput::make($name)
                        ->label($label)
                        ->maxLength(255);
            }
        }

        return $components;
    }

    /** @return array<string, array<string, mixed>> */
    private static function fieldIndex(): array
    {
        $index = [];

        foreach (self::flatFields() as $field) {
            $index[$field['id']] = $field;
        }

        return $index;
    }

    /** @return array<int, array<string, mixed>> */
    private static function flatFields(): array
    {
        return array_merge(...array_map(
            fn (array $section): array => $section['fields'],
            self::definitions(),
        ));
    }

    /** @return array<int, array{label: string, description?: string, fields: array<int, array<string, mixed>>}> */
    private static function definitions(): array
    {
        return [
            [
                'label' => 'ჩვენს შესახებ — მთავარი ბლოკი და ისტორია',
                'description' => 'გვერდის მთავარი სათაური, აღწერა, ღილაკები და კომპანიის ისტორიის ტექსტები.',
                'fields' => [
                    self::field('about_hero_title', 'about.hero.title', 'მთავარი სათაური'),
                    self::field('about_hero_description', 'about.hero.description', 'მთავარი აღწერა', 'textarea'),
                    self::field('about_hero_primary_cta', 'about.hero.cta.primary', 'პირველი ღილაკი'),
                    self::field('about_hero_secondary_cta', 'about.hero.cta.secondary', 'მეორე ღილაკი'),
                    self::field('about_story_title', 'about.story.title', 'ისტორიის სათაური'),
                    self::field('about_story_paragraph_0', 'about.story.paragraph.0', 'ისტორიის პირველი აბზაცი', 'textarea'),
                    self::field('about_story_paragraph_1', 'about.story.paragraph.1', 'ისტორიის მეორე აბზაცი', 'textarea'),
                    self::field('about_story_image_alt', 'about.story.imageAlt', 'სურათის ALT ტექსტი'),
                ],
            ],
            [
                'label' => 'ჩვენს შესახებ — ვინ ვართ და რატომ SafeTech',
                'description' => 'კომპანიის პოზიციონირება, უპირატესობები და მათი აღწერები.',
                'fields' => [
                    self::field('about_who_title', 'about.who.title', '„ვინ ვართ“ სათაური'),
                    self::field('about_who_description', 'about.who.description', '„ვინ ვართ“ აღწერა', 'textarea'),
                    self::field('about_who_item_0_title', 'about.who.item.0.title', '„ვინ ვართ“ პუნქტი 1 — სათაური'),
                    self::field('about_who_item_0_description', 'about.who.item.0.description', '„ვინ ვართ“ პუნქტი 1 — აღწერა', 'textarea'),
                    self::field('about_who_item_1_title', 'about.who.item.1.title', '„ვინ ვართ“ პუნქტი 2 — სათაური'),
                    self::field('about_who_item_1_description', 'about.who.item.1.description', '„ვინ ვართ“ პუნქტი 2 — აღწერა', 'textarea'),
                    self::field('about_who_item_2_title', 'about.who.item.2.title', '„ვინ ვართ“ პუნქტი 3 — სათაური'),
                    self::field('about_who_item_2_description', 'about.who.item.2.description', '„ვინ ვართ“ პუნქტი 3 — აღწერა', 'textarea'),
                    self::field('about_why_title', 'about.why.title', '„რატომ ჩვენ“ სათაური'),
                    self::field('about_why_description', 'about.why.description', '„რატომ ჩვენ“ აღწერა', 'textarea'),
                    self::field('about_why_item_0_title', 'about.why.item.0.title', 'უპირატესობა 1 — სათაური'),
                    self::field('about_why_item_0_description', 'about.why.item.0.description', 'უპირატესობა 1 — აღწერა', 'textarea'),
                    self::field('about_why_item_1_title', 'about.why.item.1.title', 'უპირატესობა 2 — სათაური'),
                    self::field('about_why_item_1_description', 'about.why.item.1.description', 'უპირატესობა 2 — აღწერა', 'textarea'),
                    self::field('about_why_item_2_title', 'about.why.item.2.title', 'უპირატესობა 3 — სათაური'),
                    self::field('about_why_item_2_description', 'about.why.item.2.description', 'უპირატესობა 3 — აღწერა', 'textarea'),
                    self::field('about_why_item_3_title', 'about.why.item.3.title', 'უპირატესობა 4 — სათაური'),
                    self::field('about_why_item_3_description', 'about.why.item.3.description', 'უპირატესობა 4 — აღწერა', 'textarea'),
                ],
            ],
            [
                'label' => 'ჩვენს შესახებ — რას ვაკეთებთ და როგორ ვმუშაობთ',
                'description' => 'სამუშაო მიმართულებები და პროცესის ეტაპები.',
                'fields' => [
                    self::field('about_what_item_0_index', 'about.what.item.0.index', 'მიმართულება 1 — ნომერი'),
                    self::field('about_what_item_0_title', 'about.what.item.0.title', 'მიმართულება 1 — სათაური'),
                    self::field('about_what_item_0_description', 'about.what.item.0.description', 'მიმართულება 1 — აღწერა', 'textarea'),
                    self::field('about_what_item_1_index', 'about.what.item.1.index', 'მიმართულება 2 — ნომერი'),
                    self::field('about_what_item_1_title', 'about.what.item.1.title', 'მიმართულება 2 — სათაური'),
                    self::field('about_what_item_1_description', 'about.what.item.1.description', 'მიმართულება 2 — აღწერა', 'textarea'),
                    self::field('about_what_item_2_index', 'about.what.item.2.index', 'მიმართულება 3 — ნომერი'),
                    self::field('about_what_item_2_title', 'about.what.item.2.title', 'მიმართულება 3 — სათაური'),
                    self::field('about_what_item_2_description', 'about.what.item.2.description', 'მიმართულება 3 — აღწერა', 'textarea'),
                    self::field('about_how_title', 'about.how.title', '„როგორ ვმუშაობთ“ სათაური'),
                    self::field('about_how_item_0_title', 'about.how.item.0.title', 'ეტაპი 1 — სათაური'),
                    self::field('about_how_item_0_description', 'about.how.item.0.description', 'ეტაპი 1 — აღწერა', 'textarea'),
                    self::field('about_how_item_1_title', 'about.how.item.1.title', 'ეტაპი 2 — სათაური'),
                    self::field('about_how_item_1_description', 'about.how.item.1.description', 'ეტაპი 2 — აღწერა', 'textarea'),
                    self::field('about_how_item_2_title', 'about.how.item.2.title', 'ეტაპი 3 — სათაური'),
                    self::field('about_how_item_2_description', 'about.how.item.2.description', 'ეტაპი 3 — აღწერა', 'textarea'),
                    self::field('about_how_item_3_title', 'about.how.item.3.title', 'ეტაპი 4 — სათაური'),
                    self::field('about_how_item_3_description', 'about.how.item.3.description', 'ეტაპი 4 — აღწერა', 'textarea'),
                ],
            ],
            [
                'label' => 'ჩვენს შესახებ — ციფრები, გუნდი და ბოლო მოწოდება',
                'description' => 'სტატისტიკა, გუნდის ბლოკი და გვერდის ბოლო CTA.',
                'fields' => [
                    self::field('about_numbers_item_0_value', 'about.numbers.item.0.value', 'ციფრი 1 — მნიშვნელობა'),
                    self::field('about_numbers_item_0_label', 'about.numbers.item.0.label', 'ციფრი 1 — წარწერა'),
                    self::field('about_numbers_item_1_value', 'about.numbers.item.1.value', 'ციფრი 2 — მნიშვნელობა'),
                    self::field('about_numbers_item_1_label', 'about.numbers.item.1.label', 'ციფრი 2 — წარწერა'),
                    self::field('about_numbers_item_2_value', 'about.numbers.item.2.value', 'ციფრი 3 — მნიშვნელობა'),
                    self::field('about_numbers_item_2_label', 'about.numbers.item.2.label', 'ციფრი 3 — წარწერა'),
                    self::field('about_numbers_item_3_value', 'about.numbers.item.3.value', 'ციფრი 4 — მნიშვნელობა'),
                    self::field('about_numbers_item_3_label', 'about.numbers.item.3.label', 'ციფრი 4 — წარწერა'),
                    self::field('about_team_eyebrow', 'about.team.eyebrow', 'გუნდის ზედა წარწერა'),
                    self::field('about_team_title', 'about.team.title', 'გუნდის სათაური'),
                    self::field('about_team_description', 'about.team.description', 'გუნდის აღწერა', 'textarea'),
                    self::field('about_team_region_label', 'about.team.regionLabel', 'გუნდის რეგიონის წარწერა'),
                    self::field('about_cta_title', 'about.cta.title', 'ბოლო CTA — სათაური'),
                    self::field('about_cta_description', 'about.cta.description', 'ბოლო CTA — აღწერა', 'textarea'),
                    self::field('about_cta_button', 'about.cta.button', 'ბოლო CTA — ღილაკი'),
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private static function field(string $id, string $key, string $label, string $type = 'text'): array
    {
        return compact('id', 'key', 'label', 'type');
    }
}
