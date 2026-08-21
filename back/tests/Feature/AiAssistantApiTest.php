<?php

namespace Tests\Feature;

use App\Events\LeadCreated;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\ContactLead;
use App\Models\Service;
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
            'services.openai.enabled' => true,
            'services.openai.api_key' => 'test-key',
            'services.openai.model' => 'gpt-5.6',
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

        Http::assertSent(fn ($request): bool => $request->url() === 'https://api.openai.com/v1/responses'
            && $request['model'] === 'gpt-5.6'
            && data_get($request->data(), 'reasoning.effort') === 'none'
            && $request['max_output_tokens'] === 1200
            && $request['store'] === false
        );
    }

    public function test_it_uses_the_supported_default_model_when_the_configured_model_is_blank(): void
    {
        config(['services.openai.model' => '']);

        Http::fake([
            'api.openai.com/*' => Http::response($this->textResponse('AI is available.')),
        ]);

        $this->postJson('/api/ai/chat', [
            'message' => 'Please confirm the assistant is available.',
            'locale' => 'en',
            'privacy' => true,
        ])->assertOk();

        Http::assertSent(fn ($request): bool => $request['model'] === 'gpt-5.6');
    }

    public function test_an_incomplete_openai_response_returns_a_safe_unavailable_response(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'id' => 'resp_incomplete',
                'status' => 'incomplete',
                'incomplete_details' => ['reason' => 'max_output_tokens'],
                'output' => [],
            ]),
        ]);

        $this->postJson('/api/ai/chat', [
            'message' => 'Please recommend a camera system.',
            'locale' => 'en',
            'privacy' => true,
        ])->assertStatus(503)
            ->assertJsonPath(
                'message',
                'The AI consultant is temporarily unavailable. You can still contact SafeTech by phone or WhatsApp.',
            );

        $this->assertDatabaseCount('ai_messages', 2);
        $this->assertDatabaseHas('ai_messages', ['role' => 'user']);
        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant',
            'content' => 'The AI consultant is temporarily unavailable. You can still contact SafeTech by phone or WhatsApp.',
        ]);
        $conversation = AiConversation::query()->firstOrFail();
        $this->assertStringContainsString(
            'max_output_tokens',
            (string) data_get($conversation->metadata, 'last_error.message'),
        );
    }

    public function test_it_searches_published_services_using_localized_content(): void
    {
        Service::query()->create([
            'slug' => 'video-surveillance',
            'name' => 'ვიდეო მეთვალყურეობა',
            'title' => 'ვიდეო მეთვალყურეობის სისტემები',
            'description' => 'უსაფრთხოების კამერების მონტაჟი და გამართვა.',
            'seo_description' => 'ვიდეო მეთვალყურეობის სისტემების მონტაჟი.',
            'translations' => [
                'fields' => [
                    'name' => [
                        'en' => 'Video surveillance',
                        'ru' => 'Видеонаблюдение',
                    ],
                    'title' => [
                        'en' => 'Video surveillance systems',
                        'ru' => 'Системы видеонаблюдения',
                    ],
                    'description' => [
                        'en' => 'Professional surveillance camera installation.',
                        'ru' => 'Профессиональный монтаж камер видеонаблюдения.',
                    ],
                ],
            ],
            'is_published' => true,
        ]);

        Http::fakeSequence()
            ->push($this->toolCallResponse('search_services', [
                'query' => 'surveillance',
            ]))
            ->push($this->textResponse('We install professional video surveillance systems.'));

        $chat = $this->postJson('/api/ai/chat', [
            'message' => 'I need surveillance cameras for my house.',
            'locale' => 'en',
            'privacy' => true,
        ])->assertOk();

        $assistantMessage = AiMessage::query()
            ->where('public_id', $chat->json('data.message_id'))
            ->firstOrFail();

        $this->assertSame(
            'video-surveillance',
            data_get($assistantMessage->tool_payload, '0.result.items.0.slug'),
        );
        $this->assertSame(
            'Video surveillance',
            data_get($assistantMessage->tool_payload, '0.result.items.0.name'),
        );
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
            'model' => 'gpt-5.6',
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
     * @return array<string, mixed>
     */
    private function toolCallResponse(string $name, array $arguments): array
    {
        return [
            'id' => 'resp_tool',
            'model' => 'gpt-5.6',
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
