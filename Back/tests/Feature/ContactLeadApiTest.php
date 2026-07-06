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

    public function test_it_stores_a_valid_contact_lead(): void
    {
        Event::fake([LeadCreated::class]);

        $response = $this->postJson('/api/contact-leads', [
            'firstName' => 'გიორგი',
            'lastName' => 'მაისურაძე',
            'phone' => '+995599123456',
            'email' => 'giorgi@example.com',
            'message' => 'მაინტერესებს ქსელის მოწყობა.',
            'source' => 'contact-page',
            'privacy' => true,
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'new');
        $this->assertDatabaseHas('contact_leads', [
            'email' => 'giorgi@example.com',
            'source' => 'contact-page',
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
            'service' => 'ქსელური ინფრასტრუქტურა',
            'service_slug' => 'networking',
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
            'privacy' => true,
        ]);

        $response->assertCreated();

        $lead = ContactLead::query()->latest('id')->firstOrFail();

        $this->assertSame('networking', $lead->service_slug);
        $this->assertCount(2, $lead->details ?? []);
        $this->assertSame('როუტერების რაოდენობა', $lead->details[0]['label']);
        $this->assertSame('4', $lead->details[0]['value']);
    }

    public function test_it_rejects_an_invalid_request(): void
    {
        $this->postJson('/api/contact-leads', [
            'email' => 'invalid',
            'source' => 'contact-page',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'privacy']);
    }
}
