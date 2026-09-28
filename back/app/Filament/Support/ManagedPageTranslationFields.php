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
     * @return array<string, mixed>
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
     * @return array<string, mixed>
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
     * @return array<int, TextInput|Textarea>
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
                'label' => 'Home Page: Hero and Trust',
                'fields' => [
                    self::field('home_hero_eyebrow', 'home.hero.eyebrow', 'Hero eyebrow'),
                    self::field('home_hero_title_prefix', 'home.hero.titlePrefix', 'Hero title prefix'),
                    self::field('home_hero_title_accent', 'home.hero.titleAccent', 'Hero title accent'),
                    self::field('home_hero_description', 'home.hero.description', 'Hero description', 'textarea'),
                    self::field('home_hero_primary_cta', 'home.hero.primaryCta', 'Primary CTA'),
                    self::field('home_hero_secondary_cta', 'home.hero.secondaryCta', 'Secondary CTA'),
                    self::field('home_hero_image_alt', 'home.hero.imageAlt', 'Hero image alt'),
                    self::field('home_trust_title', 'home.trust.title', 'Trust section title'),
                ],
            ],
            [
                'label' => 'Home Page: Services and Infrastructure',
                'fields' => [
                    self::field('home_services_eyebrow', 'home.services.eyebrow', 'Services eyebrow'),
                    self::field('home_services_title', 'home.services.title', 'Services title'),
                    self::field('home_services_description', 'home.services.description', 'Services description', 'textarea'),
                    self::field('home_infrastructure_eyebrow', 'home.infrastructure.eyebrow', 'Infrastructure eyebrow'),
                    self::field('home_infrastructure_title', 'home.infrastructure.title', 'Infrastructure title'),
                    self::field('home_infrastructure_description', 'home.infrastructure.description', 'Infrastructure description', 'textarea'),
                    self::field('home_infrastructure_image_alt', 'home.infrastructure.imageAlt', 'Infrastructure image alt'),
                    self::field('home_infrastructure_item_0_title', 'home.infrastructure.items.0.title', 'Infrastructure item 1 title'),
                    self::field('home_infrastructure_item_0_description', 'home.infrastructure.items.0.description', 'Infrastructure item 1 description', 'textarea'),
                    self::field('home_infrastructure_item_1_title', 'home.infrastructure.items.1.title', 'Infrastructure item 2 title'),
                    self::field('home_infrastructure_item_1_description', 'home.infrastructure.items.1.description', 'Infrastructure item 2 description', 'textarea'),
                    self::field('home_infrastructure_item_2_title', 'home.infrastructure.items.2.title', 'Infrastructure item 3 title'),
                    self::field('home_infrastructure_item_2_description', 'home.infrastructure.items.2.description', 'Infrastructure item 3 description', 'textarea'),
                ],
            ],
            [
                'label' => 'Home Page: Projects, Reviews and Why SafeTech',
                'fields' => [
                    self::field('home_projects_eyebrow', 'home.projects.eyebrow', 'Projects eyebrow'),
                    self::field('home_projects_title', 'home.projects.title', 'Projects title'),
                    self::field('home_projects_description', 'home.projects.description', 'Projects description', 'textarea'),
                    self::field('home_projects_action', 'home.projects.action', 'Projects action'),
                    self::field('home_testimonials_eyebrow', 'home.testimonials.eyebrow', 'Reviews eyebrow'),
                    self::field('home_testimonials_title', 'home.testimonials.title', 'Reviews title'),
                    self::field('home_testimonials_description', 'home.testimonials.description', 'Reviews description', 'textarea'),
                    self::field('home_testimonials_google_reviews', 'home.testimonials.googleReviews', 'Google reviews button'),
                    self::field('home_why_eyebrow', 'home.why.eyebrow', 'Why eyebrow'),
                    self::field('home_why_title', 'home.why.title', 'Why title'),
                    self::field('home_why_description', 'home.why.description', 'Why description', 'textarea'),
                    self::field('home_why_item_0_title', 'home.why.items.0.title', 'Why item 1 title'),
                    self::field('home_why_item_0_description', 'home.why.items.0.description', 'Why item 1 description', 'textarea'),
                    self::field('home_why_item_1_title', 'home.why.items.1.title', 'Why item 2 title'),
                    self::field('home_why_item_1_description', 'home.why.items.1.description', 'Why item 2 description', 'textarea'),
                    self::field('home_why_item_2_title', 'home.why.items.2.title', 'Why item 3 title'),
                    self::field('home_why_item_2_description', 'home.why.items.2.description', 'Why item 3 description', 'textarea'),
                    self::field('home_why_item_3_title', 'home.why.items.3.title', 'Why item 4 title'),
                    self::field('home_why_item_3_description', 'home.why.items.3.description', 'Why item 4 description', 'textarea'),
                    self::field('home_why_item_4_title', 'home.why.items.4.title', 'Why item 5 title'),
                    self::field('home_why_item_4_description', 'home.why.items.4.description', 'Why item 5 description', 'textarea'),
                    self::field('home_why_item_5_title', 'home.why.items.5.title', 'Why item 6 title'),
                    self::field('home_why_item_5_description', 'home.why.items.5.description', 'Why item 6 description', 'textarea'),
                ],
            ],
            [
                'label' => 'Home Page: Industries and CTA',
                'fields' => [
                    self::field('home_industries_eyebrow', 'home.industries.eyebrow', 'Industries eyebrow'),
                    self::field('home_industries_title', 'home.industries.title', 'Industries title'),
                    self::field('home_industries_description', 'home.industries.description', 'Industries description', 'textarea'),
                    self::field('home_industries_item_0', 'home.industries.items.0', 'Industry 1'),
                    self::field('home_industries_item_1', 'home.industries.items.1', 'Industry 2'),
                    self::field('home_industries_item_2', 'home.industries.items.2', 'Industry 3'),
                    self::field('home_industries_item_3', 'home.industries.items.3', 'Industry 4'),
                    self::field('home_cta_eyebrow', 'home.cta.eyebrow', 'CTA eyebrow'),
                    self::field('home_cta_title', 'home.cta.title', 'CTA title'),
                    self::field('home_cta_description', 'home.cta.description', 'CTA description', 'textarea'),
                    self::field('home_cta_email_label', 'home.cta.emailLabel', 'CTA email label'),
                    self::field('home_cta_email_placeholder', 'home.cta.emailPlaceholder', 'CTA email placeholder'),
                    self::field('home_cta_submit', 'home.cta.submit', 'CTA submit button'),
                    self::field('home_cta_note', 'home.cta.note', 'CTA note', 'textarea'),
                ],
            ],
            [
                'label' => 'Services Page: Hero and Catalog',
                'fields' => [
                    self::field('services_hero_eyebrow', 'services.hero.eyebrow', 'Hero eyebrow'),
                    self::field('services_hero_title_prefix', 'services.hero.titlePrefix', 'Hero title prefix'),
                    self::field('services_hero_title_accent', 'services.hero.titleAccent', 'Hero title accent'),
                    self::field('services_hero_title_suffix', 'services.hero.titleSuffix', 'Hero title suffix'),
                    self::field('services_hero_description', 'services.hero.description', 'Hero description', 'textarea'),
                    self::field('services_catalog_title', 'services.catalog.title', 'Catalog title'),
                    self::field('services_catalog_description', 'services.catalog.description', 'Catalog description', 'textarea'),
                    self::field('services_catalog_page', 'services.catalog.page', 'Catalog page label'),
                    self::field('services_catalog_count', 'services.catalog.count', 'Catalog count label'),
                    self::field('services_catalog_helper', 'services.catalog.helper', 'Catalog helper', 'textarea'),
                    self::field('services_featured_title', 'services.featured.title', 'Featured title'),
                ],
            ],
            [
                'label' => 'Services Page: Process, FAQ and CTA',
                'fields' => [
                    self::field('services_work_title', 'services.work.title', 'Process title'),
                    self::field('services_work_step_0_title', 'services.work.step.0.title', 'Step 1 title'),
                    self::field('services_work_step_0_description', 'services.work.step.0.description', 'Step 1 description', 'textarea'),
                    self::field('services_work_step_1_title', 'services.work.step.1.title', 'Step 2 title'),
                    self::field('services_work_step_1_description', 'services.work.step.1.description', 'Step 2 description', 'textarea'),
                    self::field('services_work_step_2_title', 'services.work.step.2.title', 'Step 3 title'),
                    self::field('services_work_step_2_description', 'services.work.step.2.description', 'Step 3 description', 'textarea'),
                    self::field('services_work_step_3_title', 'services.work.step.3.title', 'Step 4 title'),
                    self::field('services_work_step_3_description', 'services.work.step.3.description', 'Step 4 description', 'textarea'),
                    self::field('services_work_step_4_title', 'services.work.step.4.title', 'Step 5 title'),
                    self::field('services_work_step_4_description', 'services.work.step.4.description', 'Step 5 description', 'textarea'),
                    self::field('services_work_step_5_title', 'services.work.step.5.title', 'Step 6 title'),
                    self::field('services_work_step_5_description', 'services.work.step.5.description', 'Step 6 description', 'textarea'),
                    self::field('services_faq_title', 'services.faq.title', 'FAQ title'),
                    self::field('services_faq_description', 'services.faq.description', 'FAQ description', 'textarea'),
                    self::field('services_faq_contact', 'services.faq.contact', 'FAQ contact button'),
                    self::field('services_cta_title', 'services.cta.title', 'CTA title'),
                    self::field('services_cta_description', 'services.cta.description', 'CTA description', 'textarea'),
                    self::field('services_cta_quote', 'services.cta.quote', 'Quote button'),
                    self::field('services_cta_call', 'services.cta.call', 'Call button'),
                ],
            ],
            [
                'label' => 'Project and Service Detail Shared Copy',
                'fields' => [
                    self::field('projects_completed', 'projects.completed', 'Projects completed label'),
                    self::field('projects_video_open', 'projects.video.open', 'Open project video label'),
                    self::field('service_detail_cta_title_prefix', 'service.detail.cta.titlePrefix', 'Service detail CTA title'),
                    self::field('service_detail_cta_description', 'service.detail.cta.description', 'Service detail CTA description', 'textarea'),
                    self::field('service_detail_cta_consultation', 'service.detail.cta.consultation', 'Consultation button'),
                    self::field('service_detail_cta_call', 'service.detail.cta.call', 'Call button'),
                    self::field('service_detail_cta_calculator', 'service.detail.cta.calculator', 'Calculator button'),
                ],
            ],
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
