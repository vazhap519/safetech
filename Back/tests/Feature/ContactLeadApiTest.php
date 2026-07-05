<?php

namespace Tests\Feature;

use App\Events\LeadCreated;
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
        $this->assertDatabaseHas('contact_leads', ['email' => 'giorgi@example.com', 'source' => 'contact-page']);
        Event::assertDispatched(LeadCreated::class);
    }

    public function test_it_rejects_an_invalid_request(): void
    {
        $this->postJson('/api/contact-leads', ['email' => 'invalid', 'source' => 'contact-page'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'privacy']);
    }
}
