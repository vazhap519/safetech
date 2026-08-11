<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestimonialPublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exposes_only_active_testimonials_with_translation_entries(): void
    {
        $published = Testimonial::query()->create([
            'quote' => 'Reliable support for our office network.',
            'author' => 'Nino K.',
            'role' => 'Office manager',
            'company' => 'Example LLC',
            'is_active' => true,
            'sort_order' => 2,
            'translations' => [
                'fields' => [
                    'quote' => [
                        'ka' => 'საიმედო მხარდაჭერა ჩვენი ოფისის ქსელისთვის.',
                    ],
                ],
            ],
        ]);
        Testimonial::query()->create([
            'quote' => 'This draft must stay private.',
            'author' => 'Private client',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/content')->assertOk();

        $response
            ->assertJsonCount(1, 'data.testimonials')
            ->assertJsonPath('data.testimonials.0.id', $published->id)
            ->assertJsonPath('data.testimonials.0.author', 'Nino K.');

        $translationKeys = collect(
            $response->json('data.settings.translations.entries'),
        )->pluck('key');

        $this->assertTrue(
            $translationKeys->contains("testimonial.{$published->id}.quote"),
        );
    }

    public function test_it_can_project_active_locale_and_client_translations_without_changing_content(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'translations'],
            [
                'value' => [
                    'entries' => [
                        [
                            'key' => 'nav.home',
                            'ka' => 'მთავარი',
                            'en' => 'Home',
                            'ru' => 'Главная',
                        ],
                        [
                            'key' => 'service.cctv.description',
                            'ka' => 'კამერების მონტაჟი',
                            'en' => 'Camera installation',
                            'ru' => 'Монтаж камер',
                        ],
                    ],
                ],
                'is_public' => true,
            ],
        );

        $response = $this->getJson(
            '/api/content?translation_locale=en&client_translation_prefixes=nav,forms',
        )->assertOk();

        $serverEntries = collect(
            $response->json('data.settings.translations.entries'),
        )->keyBy('key');
        $clientEntries = collect(
            $response->json('data.settings.client_translations.entries'),
        )->keyBy('key');

        $this->assertSame(
            ['key' => 'nav.home', 'en' => 'Home'],
            $serverEntries->get('nav.home'),
        );
        $this->assertSame(
            ['key' => 'service.cctv.description', 'en' => 'Camera installation'],
            $serverEntries->get('service.cctv.description'),
        );
        $this->assertSame(
            [
                'key' => 'nav.home',
                'ka' => 'მთავარი',
                'en' => 'Home',
                'ru' => 'Главная',
            ],
            $clientEntries->get('nav.home'),
        );
        $this->assertFalse($clientEntries->has('service.cctv.description'));
    }

    public function test_branding_media_changes_invalidate_cached_public_content(): void
    {
        Storage::fake('public');

        $branding = SiteSetting::query()->updateOrCreate(
            ['key' => 'branding'],
            [
                'value' => ['site_name' => 'SafeTech'],
                'is_public' => true,
            ],
        );

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.settings.branding.logo', null);

        $branding
            ->addMedia(UploadedFile::fake()->image('logo.png', 320, 160))
            ->toMediaCollection('logo');

        $logo = $this->getJson('/api/content')
            ->assertOk()
            ->json('data.settings.branding.logo');

        $this->assertIsString($logo);
        $this->assertStringContainsString('/storage/', $logo);

        $branding->getFirstMedia('logo')?->delete();

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.settings.branding.logo', null);
    }
}
