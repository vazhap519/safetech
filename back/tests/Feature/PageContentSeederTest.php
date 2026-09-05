<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\SeoPage;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\ContentSeeder;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\SeoPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_core_page_content_contact_numbers_and_faqs_idempotently(): void
    {
        $this->seed(PageContentSeeder::class);
        $this->seed(PageContentSeeder::class);

        $contact = SiteSetting::query()->where('key', 'contact')->firstOrFail()->value;

        $this->assertSame('571 430 169', $contact['phone']);
        $this->assertSame(
            ['571 430 169', '557 316 310'],
            array_values($contact['phones']),
        );
        $this->assertSame('571430169', $contact['whatsapp']);

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $translationMap = MultilingualContent::mapFrom($translations->value);

        $this->assertSame(
            'მიიღეთ კონსულტაცია და შეთავაზება თქვენი ობიექტისთვის',
            $translationMap['contact.hero.title']['ka'] ?? null,
        );
        $this->assertSame(
            'Get a consultation and quote for your property',
            $translationMap['contact.hero.title']['en'] ?? null,
        );
        $this->assertSame(
            'Профессиональные технические услуги',
            $translationMap['services.hero.titlePrefix']['ru'] ?? null,
        );

        $this->assertSame(
            6,
            Faq::query()->whereNull('service_id')->where('context', 'contact')->count(),
        );
    }

    public function test_it_preserves_admin_managed_content_when_seeders_run_again(): void
    {
        $this->seed(PageContentSeeder::class);

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $map = MultilingualContent::mapFrom($translations->value);
        $map['home.hero.titlePrefix']['ka'] = 'ადმინის მიერ შეცვლილი სათაური';
        $translations->value = ['entries' => MultilingualContent::entriesFromMap($map)];
        $translations->save();

        $contact = SiteSetting::query()->where('key', 'contact')->firstOrFail();
        $contactValue = $contact->value;
        $contactValue['email'] = 'custom@example.com';
        $contact->value = $contactValue;
        $contact->save();

        $this->seed(ContentSeeder::class);
        $this->seed(PageContentSeeder::class);

        $savedMap = MultilingualContent::mapFrom(
            SiteSetting::query()->where('key', 'translations')->firstOrFail()->value,
        );
        $savedContact = SiteSetting::query()->where('key', 'contact')->firstOrFail()->value;

        $this->assertSame(
            'ადმინის მიერ შეცვლილი სათაური',
            $savedMap['home.hero.titlePrefix']['ka'] ?? null,
        );
        $this->assertSame('custom@example.com', $savedContact['email']);
        $this->assertContains('571 430 169', $savedContact['phones']);
        $this->assertContains('557 316 310', $savedContact['phones']);
    }

    public function test_it_seeds_strong_multilingual_seo_and_preserves_custom_edits(): void
    {
        $this->seed(SeoPageSeeder::class);

        $contact = SeoPage::query()->where('key', 'contact')->firstOrFail();

        $this->assertStringContainsString('571 430 169', $contact->description);
        $this->assertStringContainsString('557 316 310', $contact->description);
        $this->assertSame(
            'Contact SafeTech — IT and Security Consultation',
            data_get($contact->translations, 'fields.title.en'),
        );
        $this->assertNotEmpty(data_get($contact->translations, 'keywords.ru'));

        $contact->title = 'Custom administrator SEO title';
        $contact->save();

        $this->seed(SeoPageSeeder::class);

        $this->assertSame(
            'Custom administrator SEO title',
            SeoPage::query()->where('key', 'contact')->value('title'),
        );
    }
}
