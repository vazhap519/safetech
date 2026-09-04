<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\SeoPage;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\ContactPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_only_the_contact_page_in_all_three_languages(): void
    {
        $this->seed(ContactPageSeeder::class);

        $contact = SiteSetting::query()->where('key', 'contact')->sole()->value;
        $translationMap = MultilingualContent::mapFrom(
            SiteSetting::query()->where('key', 'translations')->sole()->value,
        );

        $this->assertSame('571 430 169', $contact['phone']);
        $this->assertSame(['571 430 169', '557 316 310'], $contact['phones']);
        $this->assertSame(
            'დაგვიკავშირდით ტექნიკური კონსულტაციისთვის',
            $translationMap['contact.hero.title']['ka'] ?? null,
        );
        $this->assertSame(
            'Contact us for a technical consultation',
            $translationMap['contact.hero.title']['en'] ?? null,
        );
        $this->assertSame(
            'Свяжитесь с нами для технической консультации',
            $translationMap['contact.hero.title']['ru'] ?? null,
        );
        $this->assertArrayNotHasKey('home.hero.titlePrefix', $translationMap);
        $this->assertDatabaseMissing('seo_pages', ['key' => 'home']);
        $this->assertSame(6, Faq::query()->where('context', 'contact')->count());
        $this->assertDatabaseHas('seo_pages', [
            'key' => 'contact',
            'slug' => '/contact',
        ]);
    }

    public function test_it_preserves_custom_contact_content_when_run_again(): void
    {
        $this->seed(ContactPageSeeder::class);

        $contact = SiteSetting::query()->where('key', 'contact')->sole();
        $contactValue = $contact->value;
        $contactValue['phone'] = '555 000 111';
        $contact->value = $contactValue;
        $contact->save();

        $translations = SiteSetting::query()->where('key', 'translations')->sole();
        $translationMap = MultilingualContent::mapFrom($translations->value);
        $translationMap['contact.hero.title']['en'] = 'Custom English contact title';
        $translations->value = ['entries' => MultilingualContent::entriesFromMap($translationMap)];
        $translations->save();

        $faq = Faq::query()
            ->where('context', 'contact')
            ->where('sort_order', 100)
            ->sole();
        $faq->question = 'Custom question';
        $faq->answer = 'Custom answer';
        $faq->save();

        $seo = SeoPage::query()->where('key', 'contact')->sole();
        $seo->title = 'Custom contact SEO title';
        $seo->noindex = true;
        $seo->save();

        $this->seed(ContactPageSeeder::class);

        $savedTranslations = MultilingualContent::mapFrom(
            SiteSetting::query()->where('key', 'translations')->sole()->value,
        );

        $this->assertSame(
            '555 000 111',
            data_get(SiteSetting::query()->where('key', 'contact')->sole()->value, 'phone'),
        );
        $this->assertSame(
            'Custom English contact title',
            $savedTranslations['contact.hero.title']['en'] ?? null,
        );
        $this->assertSame(
            'Custom question',
            Faq::query()->where('context', 'contact')->where('sort_order', 100)->value('question'),
        );
        $this->assertSame(
            'Custom answer',
            Faq::query()->where('context', 'contact')->where('sort_order', 100)->value('answer'),
        );
        $this->assertSame(
            'Custom contact SEO title',
            SeoPage::query()->where('key', 'contact')->value('title'),
        );
        $this->assertTrue((bool) SeoPage::query()->where('key', 'contact')->value('noindex'));
    }

    public function test_it_does_not_recreate_contact_records_deleted_by_an_administrator(): void
    {
        $this->seed(ContactPageSeeder::class);

        SiteSetting::query()->where('key', 'contact')->sole()->delete();
        SiteSetting::query()->where('key', 'translations')->sole()->delete();
        Faq::query()->where('context', 'contact')->where('sort_order', 100)->sole()->delete();
        SeoPage::query()->where('key', 'contact')->sole()->delete();

        $this->seed(ContactPageSeeder::class);

        $this->assertDatabaseMissing('site_settings', ['key' => 'contact']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'translations']);
        $this->assertDatabaseMissing('faqs', [
            'context' => 'contact',
            'sort_order' => 100,
        ]);
        $this->assertDatabaseMissing('seo_pages', ['key' => 'contact']);
    }
}
