<?php

namespace Tests\Unit;

use App\Support\Seo\SeoAudit;
use PHPUnit\Framework\TestCase;

class SeoAuditTest extends TestCase
{
    public function test_complete_multilingual_metadata_receives_full_internal_score(): void
    {
        $state = $this->validState();

        $audit = SeoAudit::analyze($state);

        $this->assertSame(100, $audit['score']);
        $this->assertSame([], $audit['issues']);
    }

    public function test_normalizer_repairs_mechanical_fields_without_fake_translation(): void
    {
        $state = $this->validState();
        $state['slug'] = 'wrong';
        $state['schema_type'] = null;
        $state['translations']['fields']['title']['en'] = '';
        $state['translations']['fields']['og_title']['ka'] = '';
        $state['keywords'] = [
            ['value' => '  ქსელები  '],
            ['value' => 'ქსელები'],
        ];

        $fixed = SeoAudit::normalize($state);
        $audit = SeoAudit::analyze($fixed);

        $this->assertSame('/services', $fixed['slug']);
        $this->assertSame('CollectionPage', $fixed['schema_type']);
        $this->assertSame($fixed['title'], data_get($fixed, 'translations.fields.og_title.ka'));
        $this->assertSame('', data_get($fixed, 'translations.fields.title.en'));
        $this->assertSame([['value' => 'ქსელები']], $fixed['keywords']);
        $this->assertLessThan(100, $audit['score']);
    }

    public function test_wrong_canonical_schema_and_core_noindex_are_flagged(): void
    {
        $state = $this->validState();
        $state['slug'] = '/projects';
        $state['schema_type'] = 'WebPage';
        $state['noindex'] = true;

        $audit = SeoAudit::analyze($state);

        $this->assertLessThan(100, $audit['score']);
        $this->assertTrue(collect($audit['issues'])->contains(
            fn (string $issue): bool => str_contains($issue, 'Canonical URL'),
        ));
        $this->assertTrue(collect($audit['issues'])->contains(
            fn (string $issue): bool => str_contains($issue, 'Schema.org'),
        ));
        $this->assertTrue(collect($audit['issues'])->contains(
            fn (string $issue): bool => str_contains($issue, 'Noindex'),
        ));
    }

    public function test_identical_language_copies_are_flagged(): void
    {
        $state = $this->validState();
        $sameTitle = 'Professional IT and security services for business';
        $sameDescription = 'Professional networking, server, surveillance, and managed IT support services for businesses across Georgia.';

        foreach (SeoAudit::LOCALES as $locale) {
            $state['translations']['fields']['title'][$locale] = $sameTitle;
            $state['translations']['fields']['description'][$locale] = $sameDescription;
        }

        $audit = SeoAudit::analyze($state);

        $this->assertLessThan(100, $audit['score']);
        $this->assertTrue(collect($audit['issues'])->contains(
            fn (string $issue): bool => str_contains($issue, 'ზუსტი ასლებია'),
        ));
    }

    /** @return array<string, mixed> */
    private function validState(): array
    {
        return [
            'key' => 'services',
            'slug' => '/services',
            'title' => 'IT და უსაფრთხოების სერვისები ბიზნესისთვის',
            'description' => 'SafeTech გთავაზობთ ქსელის, სერვერების, ვიდეოსამეთვალყურეობისა და IT მხარდაჭერის პროფესიონალურ სერვისებს.',
            'schema_type' => 'CollectionPage',
            'schema' => null,
            'noindex' => false,
            'translations' => [
                'fields' => [
                    'title' => [
                        'ka' => 'IT და უსაფრთხოების სერვისები ბიზნესისთვის',
                        'en' => 'Professional IT and Security Services for Business',
                        'ru' => 'Профессиональные IT-услуги и безопасность для бизнеса',
                    ],
                    'description' => [
                        'ka' => 'SafeTech გთავაზობთ ქსელის, სერვერების, ვიდეოსამეთვალყურეობისა და IT მხარდაჭერის პროფესიონალურ სერვისებს.',
                        'en' => 'SafeTech provides professional networking, server, video surveillance, and managed IT support services for businesses.',
                        'ru' => 'SafeTech предоставляет бизнесу профессиональные услуги по сетям, серверам, видеонаблюдению и IT-поддержке.',
                    ],
                    'og_title' => ['ka' => '', 'en' => '', 'ru' => ''],
                    'og_description' => ['ka' => '', 'en' => '', 'ru' => ''],
                ],
            ],
        ];
    }
}
