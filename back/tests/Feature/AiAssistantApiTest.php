<?php

namespace Tests\Feature;

use App\Events\LeadCreated;
use App\Models\AiKnowledgeCandidate;
use App\Models\AiMessage;
use App\Models\ContactLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.openai.api_key' => 'test-key',
            'services.openai.model' => 'gpt-5-mini',
        ]);
    }

    public function test_it_stores_an_ai_conversation_and_assistant_message(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response($this->textResponse('გამარჯობა! როგორ დაგეხმაროთ?')),
        ]);

        $response = $this->postJson('/api/ai/chat', [
            'message' => 'მინდა კამერების მონტაჟი.',
            'locale' => 'ka',
            'privacy' => true,
            'website' => '',
        ])->assertOk();

        $conversationId = $response->json('data.conversation_id');
        $messageId = $response->json('data.message_id');

        $this->assertNotEmpty($conversationId);
        $this->assertNotEmpty($messageId);
        $this->assertDatabaseHas('ai_conversations', [
            'public_id' => $conversationId,
            'locale' => 'ka',
        ]);
        $this->assertDatabaseHas('ai_messages', [
            'public_id' => $messageId,
            'role' => 'assistant',
            'content' => 'გამარჯობა! როგორ დაგეხმაროთ?',
        ]);
    }

    public function test_negative_feedback_creates_a_review_candidate_instead_of_auto_learning(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response($this->textResponse('ტესტური პასუხი')),
        ]);

        $chat = $this->postJson('/api/ai/chat', [
            'message' => 'რამდენი კამერა დამჭირდება სახლში?',
            'locale' => 'ka',
            'privacy' => true,
        ])->assertOk();

        $messageId = (string) $chat->json('data.message_id');

        $this->postJson("/api/ai/messages/{$messageId}/feedback", [
            'rating' => -1,
        ])->assertOk();

        $this->assertDatabaseCount('ai_knowledge_items', 0);
        $this->assertDatabaseHas('ai_knowledge_candidates', [
            'question' => 'რამდენი კამერა დამჭირდება სახლში?',
            'status' => 'pending',
            'occurrences' => 1,
        ]);
    }

    public function test_create_lead_tool_only_uses_a_phone_number_supplied_by_the_customer(): void
    {
        Event::fake([LeadCreated::class]);

        Http::fakeSequence()
            ->push($this->toolCallResponse('create_lead', [
                'name' => 'ვაჟა',
                'phone' => '+995555123456',
                'service_slug' => null,
                'city' => 'თბილისი',
                'message' => 'მინდა კონსულტაცია.',
            ]))
            ->push($this->textResponse('მოთხოვნა მიღებულია. სპეციალისტი დაგიკავშირდებათ.'));

        $this->postJson('/api/ai/chat', [
            'message' => 'მე ვარ ვაჟა, თბილისი. დამიკავშირდით +995 555 12 34 56 ნომერზე.',
            'locale' => 'ka',
            'privacy' => true,
        ])->assertOk()
            ->assertJsonPath('data.lead_created', true);

        $lead = ContactLead::query()->where('source', 'ai-assistant')->firstOrFail();
        $this->assertSame('+995555123456', $lead->phone);
        Event::assertDispatched(LeadCreated::class);
    }

    public function test_create_lead_tool_rejects_a_phone_number_not_present_in_the_conversation(): void
    {
        Http::fakeSequence()
            ->push($this->toolCallResponse('create_lead', [
                'name' => 'Test',
                'phone' => '+995555999999',
                'service_slug' => null,
                'city' => 'თბილისი',
                'message' => 'მინდა კონსულტაცია.',
            ]))
            ->push($this->textResponse('დამიტოვეთ თქვენი ტელეფონის ნომერი.'));

        $this->postJson('/api/ai/chat', [
            'message' => 'მინდა კონსულტაცია კამერებზე.',
            'locale' => 'ka',
            'privacy' => true,
        ])->assertOk()
            ->assertJsonPath('data.lead_created', false);

        $this->assertDatabaseCount('contact_leads', 0);
    }

    public function test_chat_requires_explicit_privacy_consent(): void
    {
        $this->postJson('/api/ai/chat', [
            'message' => 'Hello',
            'locale' => 'en',
            'privacy' => false,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['privacy']);
    }

    /** @return array<string, mixed> */
    private function textResponse(string $text): array
    {
        return [
            'id' => 'resp_test',
            'model' => 'gpt-5-mini',
            'output' => [[
                'id' => 'msg_test',
                'type' => 'message',
                'role' => 'assistant',
                'content' => [[
                    'type' => 'output_text',
                    'text' => $text,
                    'annotations' => [],
                ]],
            ]],
            'usage' => [
                'input_tokens' => 20,
                'output_tokens' => 10,
                'total_tokens' => 30,
            ],
        ];
    }

    /** @param array<string, mixed> $arguments
     *  @return array<string, mixed>
     */
    private function toolCallResponse(string $name, array $arguments): array
    {
        return [
            'id' => 'resp_tool',
            'model' => 'gpt-5-mini',
            'output' => [[
                'id' => 'fc_test',
                'type' => 'function_call',
                'call_id' => 'call_test',
                'name' => $name,
                'arguments' => json_encode($arguments, JSON_UNESCAPED_UNICODE),
                'status' => 'completed',
            ]],
            'usage' => [
                'input_tokens' => 20,
                'output_tokens' => 5,
                'total_tokens' => 25,
            ],
        ];
    }
}
