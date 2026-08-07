<?php

namespace Tests\Feature;

use App\Events\LeadCreated;
use App\Models\ContactLead;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ContactLeadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_frontend_origin_is_allowed_by_cors(): void
    {
        $this->withHeaders([
            'Origin' => 'https://safetech.ge',
            'Access-Control-Request-Method' => 'POST',
        ])->options('/api/contact-leads')
            ->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin', 'https://safetech.ge');
    }

    public function test_it_stores_a_valid_contact_lead(): void
    {
        Event::fake([LeadCreated::class]);

        $response = $this->postJson('/api/contact-leads', [
            'name' => 'გიორგი მაისურაძე',
            'phone' => '+995599123456',
            'email' => 'giorgi@example.com',
            'address' => 'თბილისი, საბურთალო',
            'service' => 'ქსელის მოწყობა',
            'message' => 'მაინტერესებს ქსელის მოწყობა.',
            'source' => 'home-cta',
            'privacy' => '1',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'new');
        $this->assertDatabaseHas('contact_leads', [
            'email' => 'giorgi@example.com',
            'address' => 'თბილისი, საბურთალო',
            'source' => 'home-cta',
        ]);
        Event::assertDispatched(LeadCreated::class);
    }

    public function test_it_stores_dynamic_service_details(): void
    {
        Event::fake([LeadCreated::class]);

        Service::query()->create([
            'slug' => 'networking',
            'name' => 'ქსელური ინფრასტრუქტურა',
            'title' => 'ქსელური ინფრასტრუქტურა',
            'description' => 'ტესტური აღწერა',
            'seo_description' => 'ტესტური SEO აღწერა',
            'is_published' => true,
        ]);

        $response = $this->postJson('/api/contact-leads', [
            'name' => 'ნიკა ჯაფარიძე',
            'phone' => '+995555123456',
            'email' => 'nika@example.com',
            'address' => 'თბილისი, ვაკე',
            'service' => 'ქსელური ინფრასტრუქტურა',
            'service_slug' => 'networking',
            'project_size' => 'საშუალო ობიექტი',
            'property_type' => 'სასტუმრო',
            'message' => 'საჭიროა ქსელის სრული მოწყობა.',
            'details' => [
                [
                    'key' => 'router_count',
                    'label' => 'როუტერების რაოდენობა',
                    'type' => 'number',
                    'value' => '4',
                ],
                [
                    'key' => 'switch_count',
                    'label' => 'სვიჩების რაოდენობა',
                    'type' => 'number',
                    'value' => '12',
                ],
            ],
            'source' => 'contact-page',
            'privacy' => true,
        ]);

        $response->assertCreated();

        $lead = ContactLead::query()->latest('id')->firstOrFail();

        $this->assertSame('networking', $lead->service_slug);
        $this->assertSame('თბილისი, ვაკე', $lead->address);
        $this->assertCount(2, $lead->details ?? []);
        $this->assertSame('როუტერების რაოდენობა', $lead->details[0]['label']);
        $this->assertSame('4', $lead->details[0]['value']);
    }

    public function test_it_rejects_an_incomplete_request(): void
    {
        $this->postJson('/api/contact-leads', [
            'email' => 'invalid',
            'source' => 'home-cta',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'phone',
                'email',
                'address',
                'service',
                'message',
                'privacy',
            ]);
    }

    public function test_it_localizes_responses_and_limits_dynamic_details(): void
    {
        Event::fake([LeadCreated::class]);

        $this->postJson('/api/contact-leads', [
            'name' => 'English Customer',
            'phone' => '+995555111222',
            'email' => 'english@example.com',
            'address' => 'Tbilisi',
            'service' => 'IT support',
            'message' => 'I need on-site IT support.',
            'locale' => 'en',
            'source' => 'home-cta',
            'privacy' => true,
        ])->assertCreated()
            ->assertJsonPath('message', 'Thank you. Your request was sent successfully.');

        $details = collect(range(1, 51))->map(fn (int $index): array => [
            'key' => "field_{$index}",
            'label' => "Поле {$index}",
            'type' => 'text',
            'value' => 'значение',
        ])->all();

        $this->postJson('/api/contact-leads', [
            'name' => 'Русский клиент',
            'phone' => '+995555333444',
            'email' => 'russian@example.com',
            'address' => 'Тбилиси',
            'service' => 'ИТ поддержка',
            'message' => 'Нужна помощь специалиста.',
            'locale' => 'ru',
            'details' => $details,
            'source' => 'home-cta',
            'privacy' => true,
        ])->assertUnprocessable()
            ->assertJsonPath('errors.details.0', 'Отправлено слишком много дополнительных полей.');
    }
}
