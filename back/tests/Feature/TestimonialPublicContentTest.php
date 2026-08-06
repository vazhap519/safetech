<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
