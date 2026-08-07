<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ReviewInvitation;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewInvitationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_invitation_exposes_only_safe_review_metadata(): void
    {
        config()->set('app.frontend_url', 'https://safetech.ge');

        $project = Project::query()->create([
            'slug' => 'office-network-refresh',
            'name' => 'Office network refresh',
            'title' => 'Office network refresh',
            'description' => 'A completed office network project.',
            'is_published' => true,
        ]);
        $invitation = ReviewInvitation::query()->create([
            'token' => 'review-link-token',
            'project_id' => $project->id,
            'recipient_name' => 'Nino Example',
        ]);

        $this->getJson('/api/review-invitations/review-link-token')
            ->assertOk()
            ->assertJsonPath('data.recipientName', 'Nino Example')
            ->assertJsonPath('data.projectName', 'Office network refresh');

        $this->assertSame(
            'https://safetech.ge/review/review-link-token',
            $invitation->public_url,
        );
    }

    public function test_it_creates_an_inactive_testimonial_and_consumes_the_invitation_once(): void
    {
        $invitation = ReviewInvitation::query()->create([
            'token' => 'single-use-review-token',
            'recipient_name' => 'Nino Example',
        ]);

        $payload = [
            'author' => 'Nino Example',
            'company' => 'Example LLC',
            'role' => 'Operations manager',
            'quote' => 'The installation was organised, clear, and completed on schedule.',
            'consent' => true,
            'locale' => 'en',
            'website' => '',
        ];

        $this->postJson('/api/review-invitations/single-use-review-token/submit', $payload)
            ->assertCreated()
            ->assertJsonPath('message', 'Thank you. Your review was submitted for approval.');

        $testimonial = Testimonial::query()->sole();

        $this->assertSame('Nino Example', $testimonial->author);
        $this->assertSame('Example LLC', $testimonial->company);
        $this->assertSame('Operations manager', $testimonial->role);
        $this->assertSame($payload['quote'], $testimonial->quote);
        $this->assertFalse($testimonial->is_active);

        $invitation->refresh();
        $this->assertFalse($invitation->is_active);
        $this->assertNotNull($invitation->submitted_at);
        $this->assertNotNull($invitation->consented_at);
        $this->assertSame($testimonial->id, $invitation->testimonial_id);

        $this->postJson('/api/review-invitations/single-use-review-token/submit', $payload)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'This review invitation has already been used.');

        $this->assertSame(1, Testimonial::query()->count());
    }

    public function test_inactive_or_expired_invitations_are_not_available(): void
    {
        ReviewInvitation::query()->create([
            'token' => 'inactive-review-token',
            'is_active' => false,
        ]);
        ReviewInvitation::query()->create([
            'token' => 'expired-review-token',
            'expires_at' => now()->subSecond(),
        ]);

        $this->getJson('/api/review-invitations/inactive-review-token')
            ->assertNotFound()
            ->assertJsonPath('message', 'This review invitation is unavailable or has expired.');
        $this->postJson('/api/review-invitations/inactive-review-token/submit', $this->validPayload())
            ->assertNotFound()
            ->assertJsonPath('message', 'This review invitation is unavailable or has expired.');

        $this->getJson('/api/review-invitations/expired-review-token')
            ->assertNotFound()
            ->assertJsonPath('message', 'This review invitation is unavailable or has expired.');
        $this->postJson('/api/review-invitations/expired-review-token/submit', $this->validPayload())
            ->assertNotFound()
            ->assertJsonPath('message', 'This review invitation is unavailable or has expired.');
    }

    public function test_submission_requires_review_content_and_consent(): void
    {
        ReviewInvitation::query()->create(['token' => 'validation-review-token']);

        $this->postJson('/api/review-invitations/validation-review-token/submit', [
            'author' => '',
            'quote' => '',
            'consent' => false,
            'locale' => 'ka',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['author', 'quote', 'consent'])
            ->assertJsonPath('errors.author.0', 'მიუთითეთ თქვენი სახელი.')
            ->assertJsonPath('errors.quote.0', 'დაწერეთ თქვენი შეფასება.')
            ->assertJsonPath('errors.consent.0', 'შეფასების გაგზავნამდე თანხმობა აუცილებელია.');

        $this->assertSame(0, Testimonial::query()->count());
        $this->assertNull(
            ReviewInvitation::query()->where('token', 'validation-review-token')->value('submitted_at'),
        );
    }

    public function test_submission_success_message_is_localized(): void
    {
        ReviewInvitation::query()->create(['token' => 'russian-review-token']);

        $payload = $this->validPayload();
        $payload['locale'] = 'ru';

        $this->postJson('/api/review-invitations/russian-review-token/submit', $payload)
            ->assertCreated()
            ->assertJsonPath('message', 'Спасибо. Ваш отзыв отправлен на проверку.');
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'author' => 'Nino Example',
            'quote' => 'A concise, genuine review.',
            'consent' => true,
            'website' => '',
        ];
    }
}
