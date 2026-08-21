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

    private Service $publishedService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishedService = $this->createPublishedService();
    }

    public function test_production_frontend_origin_is_allowed_by_cors(): void
    {
        $this->withHeaders([
            'Origin' => 'https://safetech.ge',
            'Access-Control-Request-Method' => 'POST',
        ])->options('/api/contact-leads')
            ->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin', 'https://safetech.ge');
    }

    public function test_it_stores_a_complete_consultation_and_derives_the_service_name(): void
    {
        Event::fake([LeadCreated::class]);

        $response = $this->postJson('/api/contact-leads', $this->validPayload([
            'firstName' => 'Giorgi',
            'lastName' => 'Customer',
            'source' => 'home-cta',
            // This untrusted value must never be persisted instead of the
            // selected service's published name.
            'service' => 'Untrusted service name',
        ]));

        $response->assertCreated()->assertJsonPath('data.status', 'new');
        $this->assertDatabaseHas('contact_leads', [
            'name' => 'Giorgi Customer',
            'email' => 'customer@example.com',
            'address' => 'თბილისი, ვაკე',
            'service' => $this->publishedService->name,
            'service_slug' => $this->publishedService->slug,
            'source' => 'home-cta',
        ]);
        Event::assertDispatched(LeadCreated::class);
    }

    public function test_it_stores_dynamic_service_details_for_a_complete_lead(): void
    {
        Event::fake([LeadCreated::class]);
        $service = $this->createPublishedService([
            'slug' => 'networking',
            'name' => 'ქსელური ინფრასტრუქტურა',
            'title' => 'ქსელური ინფრასტრუქტურა',
        ]);

        $response = $this->postJson('/api/contact-leads', $this->validPayload([
            'serviceSlug' => $service->slug,
            'project_size' => 'საშუალო ობიექტი',
            'property_type' => 'სასტუმრო',
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
        ]));

        $response->assertCreated();

        $lead = ContactLead::query()->latest('id')->firstOrFail();

        $this->assertSame($service->slug, $lead->service_slug);
        $this->assertSame($service->name, $lead->service);
        $this->assertSame('თბილისი, ვაკე', $lead->address);
        $this->assertCount(2, $lead->details ?? []);
        $this->assertSame('როუტერების რაოდენობა', $lead->details[0]['label']);
        $this->assertSame('4', $lead->details[0]['value']);
    }

    public function test_consultation_popup_accepts_only_a_published_service_slug(): void
    {
        $this->createPublishedService([
            'slug' => 'draft-service',
            'name' => 'Draft service',
            'is_published' => false,
        ]);

        foreach (['draft-service', 'missing-service'] as $slug) {
            $this->postJson('/api/contact-leads', $this->validPayload([
                'email' => $slug.'@example.com',
                'serviceSlug' => $slug,
            ]))
                ->assertUnprocessable()
                ->assertJsonValidationErrors(['service_slug']);
        }
    }

    public function test_every_public_source_rejects_incomplete_consultation_data(): void
    {
        foreach (['consultation-popup', 'home-cta', 'contact-page'] as $source) {
            $this->postJson('/api/contact-leads', [
                'email' => 'invalid',
                'source' => $source,
            ])
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'first_name',
                    'last_name',
                    'phone',
                    'email',
                    'address',
                    'service_slug',
                    'message',
                    'privacy',
                ]);
        }

        $this->assertDatabaseCount('contact_leads', 0);
    }

    public function test_it_rejects_an_unknown_public_source(): void
    {
        $this->postJson('/api/contact-leads', $this->validPayload([
            'source' => 'unknown-source',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['source']);
    }

    public function test_it_localizes_responses_and_limits_dynamic_details(): void
    {
        Event::fake([LeadCreated::class]);

        $this->postJson('/api/contact-leads', $this->validPayload([
            'locale' => 'en',
            'source' => 'home-cta',
        ]))->assertCreated()
            ->assertJsonPath('message', 'Thank you. Your request was sent successfully.');

        $details = collect(range(1, 51))->map(fn (int $index): array => [
            'key' => "field_{$index}",
            'label' => "Поле {$index}",
            'type' => 'text',
            'value' => 'значение',
        ])->all();

        $this->postJson('/api/contact-leads', $this->validPayload([
            'email' => 'russian@example.com',
            'locale' => 'ru',
            'details' => $details,
        ]))->assertUnprocessable()
            ->assertJsonPath('errors.details.0', 'Отправлено слишком много дополнительных полей.');
    }

    /** @param array<string, mixed> $overrides */
    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'firstName' => 'Test',
            'lastName' => 'Customer',
            'phone' => '+995555123456',
            'email' => 'customer@example.com',
            'address' => 'თბილისი, ვაკე',
            'serviceSlug' => $this->publishedService->slug,
            'message' => 'საჭიროა ტექნიკური კონსულტაცია ამ პროექტისთვის.',
            'source' => 'consultation-popup',
            'privacy' => true,
        ], $overrides);
    }

    /** @param array<string, mixed> $attributes */
    private function createPublishedService(array $attributes = []): Service
    {
        return Service::query()->create(array_replace([
            'slug' => 'consultation-service',
            'name' => 'Published IT support',
            'title' => 'Published IT support',
            'description' => 'A published service for consultation tests.',
            'seo_description' => 'A published service for consultation tests.',
            'is_published' => true,
        ], $attributes));
    }
}
