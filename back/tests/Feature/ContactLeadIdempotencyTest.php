<?php

namespace Tests\Feature;

use App\Events\LeadCreated;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ContactLeadIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_retrying_the_same_submission_key_creates_only_one_lead_and_one_event(): void
    {
        Event::fake([LeadCreated::class]);
        $service = $this->publishedService();
        $payload = $this->payload($service);
        $headers = ['Idempotency-Key' => 'lead-retry-test-12345678'];

        $first = $this->withHeaders($headers)->postJson('/api/contact-leads', $payload);
        $second = $this->withHeaders($headers)->postJson('/api/contact-leads', $payload);

        $first->assertCreated()->assertJsonPath('data.replayed', false);
        $second->assertOk()->assertJsonPath('data.replayed', true);
        $this->assertSame(
            $first->json('data.id'),
            $second->json('data.id'),
        );
        $this->assertDatabaseCount('contact_leads', 1);
        Event::assertDispatchedTimes(LeadCreated::class, 1);
    }

    public function test_same_submission_key_cannot_be_reused_for_different_payload(): void
    {
        Event::fake([LeadCreated::class]);
        $service = $this->publishedService();
        $headers = ['Idempotency-Key' => 'lead-conflict-test-12345678'];

        $first = $this->withHeaders($headers)->postJson(
            '/api/contact-leads',
            $this->payload($service),
        );
        $changedPayload = $this->payload($service);
        $changedPayload['message'] = 'This is a different request using the same submission key.';
        $second = $this->withHeaders($headers)->postJson(
            '/api/contact-leads',
            $changedPayload,
        );

        $first->assertCreated();
        $second->assertStatus(409);
        $this->assertDatabaseCount('contact_leads', 1);
        Event::assertDispatchedTimes(LeadCreated::class, 1);
    }

    public function test_retry_stays_idempotent_after_the_cache_is_lost(): void
    {
        Event::fake([LeadCreated::class]);
        $service = $this->publishedService();
        $payload = $this->payload($service);
        $headers = ['Idempotency-Key' => 'lead-durable-test-12345678'];

        $first = $this->withHeaders($headers)->postJson('/api/contact-leads', $payload);
        Cache::flush();
        $second = $this->withHeaders($headers)->postJson('/api/contact-leads', $payload);

        $first->assertCreated();
        $second->assertOk()->assertJsonPath('data.replayed', true);
        $this->assertDatabaseCount('contact_leads', 1);
        Event::assertDispatchedTimes(LeadCreated::class, 1);
    }

    public function test_same_key_with_different_payload_is_rejected_after_cache_loss(): void
    {
        Event::fake([LeadCreated::class]);
        $service = $this->publishedService();
        $headers = ['Idempotency-Key' => 'lead-cache-conflict-12345678'];

        $this->withHeaders($headers)
            ->postJson('/api/contact-leads', $this->payload($service))
            ->assertCreated();

        Cache::flush();

        $changedPayload = $this->payload($service);
        $changedPayload['message'] = 'A changed request after the idempotency cache was lost.';

        $this->withHeaders($headers)
            ->postJson('/api/contact-leads', $changedPayload)
            ->assertStatus(409);

        $this->assertDatabaseCount('contact_leads', 1);
        Event::assertDispatchedTimes(LeadCreated::class, 1);
    }

    public function test_distinct_submission_keys_allow_independent_requests(): void
    {
        Event::fake([LeadCreated::class]);
        $service = $this->publishedService();
        $payload = $this->payload($service);

        $first = $this->withHeaders(['Idempotency-Key' => 'lead-distinct-one-12345678'])
            ->postJson('/api/contact-leads', $payload);
        $second = $this->withHeaders(['Idempotency-Key' => 'lead-distinct-two-12345678'])
            ->postJson('/api/contact-leads', $payload);

        $first->assertCreated();
        $second->assertCreated();
        $this->assertNotSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('contact_leads', 2);
        Event::assertDispatchedTimes(LeadCreated::class, 2);
    }

    public function test_invalid_idempotency_key_is_rejected_without_creating_a_lead(): void
    {
        $service = $this->publishedService();

        $this->withHeaders(['Idempotency-Key' => 'bad key'])
            ->postJson('/api/contact-leads', $this->payload($service))
            ->assertUnprocessable();

        $this->assertDatabaseCount('contact_leads', 0);
    }

    private function publishedService(): Service
    {
        return Service::query()->create([
            'slug' => 'idempotency-service',
            'name' => 'Idempotency service',
            'title' => 'Idempotency service',
            'description' => 'Service used by idempotency tests.',
            'seo_description' => 'Service used by idempotency tests.',
            'is_published' => true,
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(Service $service): array
    {
        return [
            'firstName' => 'Retry',
            'lastName' => 'Customer',
            'phone' => '+995555123456',
            'email' => 'retry@example.com',
            'address' => 'Tbilisi',
            'serviceSlug' => $service->slug,
            'message' => 'Please process this request exactly once.',
            'source' => 'contact-page',
            'privacy' => true,
        ];
    }
}
