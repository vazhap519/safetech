<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

final class ManagedPageTranslationFields
{
    /** @return array<int, Section> */
    public static function sections(): array
    {
        return array_map(
            fn (array $section): Section => Section::make($section['label'])
                ->schema(self::componentsFor($section['fields']))
                ->columns(3)
                ->collapsed()
                ->visible(fn (Get $get): bool => $get('key') === 'translations'),
            self::definitions(),
        );
    }

    /** @param array<string, mixed> $data
     *  @return array<string, mixed>
     */
    public static function hydrate(array $data): array
    {
        if (($data['key'] ?? null) !== 'translations') {
            return $data;
        }

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

        $data['managed_page_translations'] = $managed;

        return $data;
    }

    /** @param array<string, mixed> $data
     *  @return array<string, mixed>
     */
    public static function dehydrate(array $data): array
    {
        if (($data['key'] ?? null) !== 'translations') {
            unset($data['managed_page_translations']);

            return $data;
        }

        $existingEntries = collect(data_get($data, 'value.entries', []))
            ->filter(fn ($entry): bool => is_array($entry) && filled($entry['key'] ?? null))
            ->reject(fn (array $entry): bool => in_array((string) $entry['key'], self::managedKeys(), true))
            ->values()
            ->all();

        $managedEntries = collect(self::fieldIndex())
            ->map(function (array $field, string $id) use ($data): ?array {
                $values = data_get($data, "managed_page_translations.{$id}", []);

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

        data_set($data, 'value.entries', [...$existingEntries, ...$managedEntries]);
        unset($data['managed_page_translations']);

        return $data;
    }

    /** @return array<int, string> */
    public static function managedKeys(): array
    {
        return array_values(array_map(
            fn (array $field): string => $field['key'],
            self::flatFields(),
        ));
    }

    /** @param array<int, array<string, mixed>> $fields
     *  @return array<int, TextInput|Textarea>
     */
    private static function componentsFor(array $fields): array
    {
        $components = [];

        foreach ($fields as $field) {
            foreach (['ka' => 'KA', 'en' => 'EN', 'ru' => 'RU'] as $locale => $label) {
                $name = "managed_page_translations.{$field['id']}.{$locale}";
                $componentLabel = "{$field['label']} ({$label})";

                $components[] = ($field['type'] ?? 'text') === 'textarea'
                    ? Textarea::make($name)
                        ->label($componentLabel)
                        ->rows((int) ($field['rows'] ?? 3))
                    : TextInput::make($name)
                        ->label($componentLabel)
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

    /** @return array<int, array{label: string, fields: array<int, array<string, mixed>>}> */
    private static function definitions(): array
    {
        return [
            [
                'label' => 'About Page: Hero and Story',
                'fields' => [
                    self::field('about_hero_title', 'about.hero.title', 'Hero title'),
                    self::field('about_hero_description', 'about.hero.description', 'Hero description', 'textarea'),
                    self::field('about_hero_primary_cta', 'about.hero.cta.primary', 'Hero primary button'),
                    self::field('about_hero_secondary_cta', 'about.hero.cta.secondary', 'Hero secondary button'),
                    self::field('about_story_title', 'about.story.title', 'Story title'),
                    self::field('about_story_paragraph_0', 'about.story.paragraph.0', 'Story paragraph 1', 'textarea'),
                    self::field('about_story_paragraph_1', 'about.story.paragraph.1', 'Story paragraph 2', 'textarea'),
                    self::field('about_story_image_alt', 'about.story.imageAlt', 'Story image alt'),
                ],
            ],
            [
                'label' => 'About Page: Who and Why',
                'fields' => [
                    self::field('about_who_title', 'about.who.title', 'Who title'),
                    self::field('about_who_description', 'about.who.description', 'Who description', 'textarea'),
                    self::field('about_who_item_0_title', 'about.who.item.0.title', 'Who item 1 title'),
                    self::field('about_who_item_0_description', 'about.who.item.0.description', 'Who item 1 description', 'textarea'),
                    self::field('about_who_item_1_title', 'about.who.item.1.title', 'Who item 2 title'),
                    self::field('about_who_item_1_description', 'about.who.item.1.description', 'Who item 2 description', 'textarea'),
                    self::field('about_who_item_2_title', 'about.who.item.2.title', 'Who item 3 title'),
                    self::field('about_who_item_2_description', 'about.who.item.2.description', 'Who item 3 description', 'textarea'),
                    self::field('about_why_title', 'about.why.title', 'Why title'),
                    self::field('about_why_description', 'about.why.description', 'Why description', 'textarea'),
                    self::field('about_why_item_0_title', 'about.why.item.0.title', 'Why item 1 title'),
                    self::field('about_why_item_0_description', 'about.why.item.0.description', 'Why item 1 description', 'textarea'),
                    self::field('about_why_item_1_title', 'about.why.item.1.title', 'Why item 2 title'),
                    self::field('about_why_item_1_description', 'about.why.item.1.description', 'Why item 2 description', 'textarea'),
                    self::field('about_why_item_2_title', 'about.why.item.2.title', 'Why item 3 title'),
                    self::field('about_why_item_2_description', 'about.why.item.2.description', 'Why item 3 description', 'textarea'),
                    self::field('about_why_item_3_title', 'about.why.item.3.title', 'Why item 4 title'),
                    self::field('about_why_item_3_description', 'about.why.item.3.description', 'Why item 4 description', 'textarea'),
                ],
            ],
            [
                'label' => 'About Page: What and How',
                'fields' => [
                    self::field('about_what_item_0_index', 'about.what.item.0.index', 'What item 1 index'),
                    self::field('about_what_item_0_title', 'about.what.item.0.title', 'What item 1 title'),
                    self::field('about_what_item_0_description', 'about.what.item.0.description', 'What item 1 description', 'textarea'),
                    self::field('about_what_item_1_index', 'about.what.item.1.index', 'What item 2 index'),
                    self::field('about_what_item_1_title', 'about.what.item.1.title', 'What item 2 title'),
                    self::field('about_what_item_1_description', 'about.what.item.1.description', 'What item 2 description', 'textarea'),
                    self::field('about_what_item_2_index', 'about.what.item.2.index', 'What item 3 index'),
                    self::field('about_what_item_2_title', 'about.what.item.2.title', 'What item 3 title'),
                    self::field('about_what_item_2_description', 'about.what.item.2.description', 'What item 3 description', 'textarea'),
                    self::field('about_how_title', 'about.how.title', 'How title'),
                    self::field('about_how_item_0_title', 'about.how.item.0.title', 'How step 1 title'),
                    self::field('about_how_item_0_description', 'about.how.item.0.description', 'How step 1 description', 'textarea'),
                    self::field('about_how_item_1_title', 'about.how.item.1.title', 'How step 2 title'),
                    self::field('about_how_item_1_description', 'about.how.item.1.description', 'How step 2 description', 'textarea'),
                    self::field('about_how_item_2_title', 'about.how.item.2.title', 'How step 3 title'),
                    self::field('about_how_item_2_description', 'about.how.item.2.description', 'How step 3 description', 'textarea'),
                    self::field('about_how_item_3_title', 'about.how.item.3.title', 'How step 4 title'),
                    self::field('about_how_item_3_description', 'about.how.item.3.description', 'How step 4 description', 'textarea'),
                ],
            ],
            [
                'label' => 'About Page: Numbers, Team and CTA',
                'fields' => [
                    self::field('about_numbers_item_0_value', 'about.numbers.item.0.value', 'Number item 1 value'),
                    self::field('about_numbers_item_0_label', 'about.numbers.item.0.label', 'Number item 1 label'),
                    self::field('about_numbers_item_1_value', 'about.numbers.item.1.value', 'Number item 2 value'),
                    self::field('about_numbers_item_1_label', 'about.numbers.item.1.label', 'Number item 2 label'),
                    self::field('about_numbers_item_2_value', 'about.numbers.item.2.value', 'Number item 3 value'),
                    self::field('about_numbers_item_2_label', 'about.numbers.item.2.label', 'Number item 3 label'),
                    self::field('about_numbers_item_3_value', 'about.numbers.item.3.value', 'Number item 4 value'),
                    self::field('about_numbers_item_3_label', 'about.numbers.item.3.label', 'Number item 4 label'),
                    self::field('about_team_eyebrow', 'about.team.eyebrow', 'Team eyebrow'),
                    self::field('about_team_title', 'about.team.title', 'Team title'),
                    self::field('about_team_description', 'about.team.description', 'Team description', 'textarea'),
                    self::field('about_team_region_label', 'about.team.regionLabel', 'Team region label'),
                    self::field('about_cta_title', 'about.cta.title', 'CTA title'),
                    self::field('about_cta_description', 'about.cta.description', 'CTA description', 'textarea'),
                    self::field('about_cta_button', 'about.cta.button', 'CTA button'),
                ],
            ],
            [
                'label' => 'Contact Page: Hero and Intro',
                'fields' => [
                    self::field('contact_hero_title', 'contact.hero.title', 'Hero title'),
                    self::field('contact_hero_description', 'contact.hero.description', 'Hero description', 'textarea'),
                    self::field('contact_hero_button', 'contact.hero.button', 'Hero button'),
                    self::field('contact_intro_title', 'contact.intro.title', 'Intro title'),
                    self::field('contact_intro_paragraph_0', 'contact.intro.paragraph.0', 'Intro paragraph 1', 'textarea'),
                    self::field('contact_intro_paragraph_1', 'contact.intro.paragraph.1', 'Intro paragraph 2', 'textarea'),
                    self::field('contact_intro_badge_0', 'contact.intro.badge.0', 'Intro badge 1'),
                    self::field('contact_intro_badge_1', 'contact.intro.badge.1', 'Intro badge 2'),
                    self::field('contact_intro_image_alt', 'contact.intro.imageAlt', 'Intro image alt'),
                ],
            ],
            [
                'label' => 'Contact Page: Form, Side Block and Info Labels',
                'fields' => [
                    self::field('contact_form_title', 'contact.form.title', 'Form title'),
                    self::field('contact_side_title', 'contact.side.title', 'Side block title'),
                    self::field('contact_side_description', 'contact.side.description', 'Side block description', 'textarea'),
                    self::field('contact_info_phone', 'contact.info.phone', 'Phone label'),
                    self::field('contact_info_email', 'contact.info.email', 'Email label'),
                    self::field('contact_info_address', 'contact.info.address', 'Address label'),
                    self::field('contact_info_hours', 'contact.info.hours', 'Hours label'),
                ],
            ],
            [
                'label' => 'Contact Page: Support, FAQ and Final CTA',
                'fields' => [
                    self::field('contact_support_title', 'contact.support.title', 'Support title'),
                    self::field('contact_support_description', 'contact.support.description', 'Support description', 'textarea'),
                    self::field('contact_support_badge', 'contact.support.badge', 'Support badge'),
                    self::field('contact_support_image_alt', 'contact.support.imageAlt', 'Support image alt'),
                    self::field('contact_support_item_0', 'contact.support.item.0', 'Support item 1'),
                    self::field('contact_support_item_1', 'contact.support.item.1', 'Support item 2'),
                    self::field('contact_support_item_2', 'contact.support.item.2', 'Support item 3'),
                    self::field('contact_support_item_3', 'contact.support.item.3', 'Support item 4'),
                    self::field('contact_support_item_4', 'contact.support.item.4', 'Support item 5'),
                    self::field('contact_support_item_5', 'contact.support.item.5', 'Support item 6'),
                    self::field('contact_faq_title', 'contact.faq.title', 'FAQ title'),
                    self::field('contact_final_title', 'contact.final.title', 'Final CTA title'),
                    self::field('contact_final_button', 'contact.final.button', 'Final CTA button'),
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private static function field(
        string $id,
        string $key,
        string $label,
        string $type = 'text',
        int $rows = 3,
    ): array {
        return compact('id', 'key', 'label', 'type', 'rows');
    }
}
