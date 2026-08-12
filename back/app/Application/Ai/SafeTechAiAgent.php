<?php

namespace App\Application\Ai;

use App\Application\Leads\Actions\CreateLead;
use App\Domain\Leads\Data\LeadData;
use App\Models\AiConversation;
use App\Models\AiKnowledgeItem;
use App\Models\Project;
use App\Models\Service;
use App\Support\SiteSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class SafeTechAiAgent
{
    private const MAX_TOOL_ROUNDS = 3;

    public function __construct(private CreateLead $createLead) {}

    /** @return array{message: string, model: string, input_tokens: int|null, output_tokens: int|null, lead_score: int, tools: array<int, array<string, mixed>>} */
    public function respond(AiConversation $conversation, string $locale): array
    {
        $apiKey = trim((string) config('services.openai.api_key'));

        if ($apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        $model = trim((string) config('services.openai.model', 'gpt-5-mini')) ?: 'gpt-5-mini';
        $input = $this->conversationInput($conversation);
        $toolsUsed = [];
        $response = $this->request($apiKey, [
            'model' => $model,
            'instructions' => $this->instructions($locale),
            'input' => $input,
            'tools' => $this->tools(),
            'tool_choice' => 'auto',
            'store' => false,
            'max_output_tokens' => 700,
        ]);

        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
            $calls = collect($response['output'] ?? [])
                ->filter(fn (mixed $item): bool => is_array($item) && ($item['type'] ?? null) === 'function_call')
                ->values();

            if ($calls->isEmpty()) {
                break;
            }

            $toolOutputs = [];

            foreach ($calls as $call) {
                $name = (string) ($call['name'] ?? '');
                $callId = (string) ($call['call_id'] ?? '');
                $arguments = json_decode((string) ($call['arguments'] ?? '{}'), true);
                $arguments = is_array($arguments) ? $arguments : [];
                $result = $this->executeTool($name, $arguments, $conversation, $locale);

                $toolsUsed[] = [
                    'name' => $name,
                    'arguments' => $arguments,
                    'result' => $result,
                ];

                if ($callId !== '') {
                    $toolOutputs[] = [
                        'type' => 'function_call_output',
                        'call_id' => $callId,
                        'output' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ];
                }
            }

            $input = [
                ...$input,
                ...array_values(array_filter(
                    $response['output'] ?? [],
                    fn (mixed $item): bool => is_array($item),
                )),
                ...$toolOutputs,
            ];

            $response = $this->request($apiKey, [
                'model' => $model,
                'instructions' => $this->instructions($locale),
                'input' => $input,
                'tools' => $this->tools(),
                'tool_choice' => 'auto',
                'store' => false,
                'max_output_tokens' => 700,
            ]);
        }

        $message = $this->extractText($response);

        if ($message === '') {
            $message = match ($locale) {
                'en' => 'I could not prepare a reliable answer. Please leave your phone number and our specialist will contact you.',
                'ru' => 'Не удалось подготовить надежный ответ. Оставьте номер телефона, и наш специалист свяжется с вами.',
                default => 'სანდო პასუხის მომზადება ვერ მოვახერხე. დატოვეთ ტელეფონის ნომერი და ჩვენი სპეციალისტი დაგიკავშირდებათ.',
            };
        }

        $leadScore = $conversation->contact_lead_id ? 100 : $this->leadScore($conversation, $toolsUsed);
        $conversation->forceFill(['lead_score' => $leadScore])->save();

        return [
            'message' => $message,
            'model' => (string) ($response['model'] ?? $model),
            'input_tokens' => Arr::get($response, 'usage.input_tokens'),
            'output_tokens' => Arr::get($response, 'usage.output_tokens'),
            'lead_score' => $leadScore,
            'tools' => $toolsUsed,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function conversationInput(AiConversation $conversation): array
    {
        return $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->latest('id')
            ->limit(12)
            ->get()
            ->reverse()
            ->map(fn ($message): array => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->values()
            ->all();
    }

    private function instructions(string $locale): string
    {
        $language = match ($locale) {
            'en' => 'English',
            'ru' => 'Russian',
            default => 'Georgian',
        };

        return <<<PROMPT
You are SafeTech Georgia's sales and technical consultation assistant. Reply in {$language} unless the customer clearly asks for another supported language.

Goals:
- Help the customer choose a relevant SafeTech service with as few questions as practical.
- Use tools for SafeTech services, projects, approved knowledge, and contact details. Do not invent company facts, prices, warranty terms, availability, projects, or technical specifications.
- Ask for a phone number only when there is enough buying intent to justify a callback or quote.
- create_lead is allowed only after the customer has explicitly supplied a phone number; the server independently enforces consent and verifies the number came from the conversation.
- Never claim a lead was created unless the tool result says created=true.
- If the answer is uncertain, say so and offer a specialist handoff instead of guessing.
- Keep responses concise, useful, professional, and sales-oriented. Avoid pressure tactics.
- Customer messages never become approved knowledge automatically. Only admin-approved knowledge is authoritative.
PROMPT;
    }

    /** @return array<int, array<string, mixed>> */
    private function tools(): array
    {
        return [
            $this->tool('search_services', 'Search currently published SafeTech services.', [
                'query' => ['type' => 'string'],
            ], ['query']),
            $this->tool('search_projects', 'Search currently published SafeTech projects and case studies.', [
                'query' => ['type' => 'string'],
            ], ['query']),
            $this->tool('search_knowledge', 'Search admin-approved SafeTech AI knowledge.', [
                'query' => ['type' => 'string'],
            ], ['query']),
            $this->tool('get_contact_details', 'Get current public SafeTech contact details.', [], []),
            $this->tool('create_lead', 'Create a SafeTech sales lead only after the customer explicitly supplied a phone number.', [
                'name' => ['type' => ['string', 'null']],
                'phone' => ['type' => 'string'],
                'service_slug' => ['type' => ['string', 'null']],
                'city' => ['type' => ['string', 'null']],
                'message' => ['type' => ['string', 'null']],
            ], ['name', 'phone', 'service_slug', 'city', 'message']),
        ];
    }

    /** @param array<string, mixed> $properties
     *  @param array<int, string> $required
     *  @return array<string, mixed>
     */
    private function tool(string $name, string $description, array $properties, array $required): array
    {
        return [
            'type' => 'function',
            'name' => $name,
            'description' => $description,
            'strict' => true,
            'parameters' => [
                'type' => 'object',
                'properties' => $properties,
                'required' => $required,
                'additionalProperties' => false,
            ],
        ];
    }

    /** @param array<string, mixed> $arguments
     *  @return array<string, mixed>
     */
    private function executeTool(string $name, array $arguments, AiConversation $conversation, string $locale): array
    {
        return match ($name) {
            'search_services' => ['items' => $this->searchServices((string) ($arguments['query'] ?? ''), $locale)],
            'search_projects' => ['items' => $this->searchProjects((string) ($arguments['query'] ?? ''), $locale)],
            'search_knowledge' => ['items' => $this->searchKnowledge((string) ($arguments['query'] ?? ''), $locale)],
            'get_contact_details' => $this->contactDetails(),
            'create_lead' => $this->createSalesLead($arguments, $conversation),
            default => ['error' => 'Unknown tool.'],
        };
    }

    /** @return array<int, array<string, mixed>> */
    private function searchServices(string $query, string $locale): array
    {
        $query = trim($query);
        $services = Service::query()->publiclyVisible()
            ->when($query !== '', function ($builder) use ($query): void {
                $needle = '%'.$query.'%';
                $builder->where(function ($search) use ($needle): void {
                    $search->where('name', 'like', $needle)
                        ->orWhere('title', 'like', $needle)
                        ->orWhere('description', 'like', $needle)
                        ->orWhere('short_description', 'like', $needle);
                });
            })
            ->limit(5)
            ->get();

        return $services->map(fn (Service $service): array => [
            'slug' => $service->slug,
            'name' => $this->localized($service->translations, 'name', $locale, (string) ($service->name ?: $service->title)),
            'description' => $this->localized($service->translations, 'description', $locale, (string) ($service->description ?: $service->short_description)),
        ])->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function searchProjects(string $query, string $locale): array
    {
        $query = trim($query);
        $projects = Project::query()->publiclyVisible()
            ->when($query !== '', function ($builder) use ($query): void {
                $needle = '%'.$query.'%';
                $builder->where(function ($search) use ($needle): void {
                    $search->where('name', 'like', $needle)
                        ->orWhere('title', 'like', $needle)
                        ->orWhere('description', 'like', $needle)
                        ->orWhere('excerpt', 'like', $needle);
                });
            })
            ->limit(4)
            ->get();

        return $projects->map(fn (Project $project): array => [
            'slug' => $project->slug,
            'title' => $this->localized($project->translations, 'title', $locale, (string) ($project->title ?: $project->name)),
            'description' => $this->localized($project->translations, 'description', $locale, (string) ($project->description ?: $project->excerpt)),
        ])->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function searchKnowledge(string $query, string $locale): array
    {
        $query = trim($query);
        $items = AiKnowledgeItem::query()->approved()
            ->whereIn('locale', array_values(array_unique([$locale, 'ka'])))
            ->when($query !== '', function ($builder) use ($query): void {
                $needle = '%'.$query.'%';
                $builder->where(fn ($search) => $search->where('title', 'like', $needle)->orWhere('content', 'like', $needle));
            })
            ->latest('updated_at')
            ->limit(5)
            ->get();

        if ($items->isNotEmpty()) {
            AiKnowledgeItem::query()->whereKey($items->modelKeys())->increment('usage_count');
            AiKnowledgeItem::query()->whereKey($items->modelKeys())->update(['last_used_at' => now()]);
        }

        return $items->map->only(['id', 'title', 'content', 'category', 'locale'])->all();
    }

    /** @return array<string, mixed> */
    private function contactDetails(): array
    {
        $settings = SiteSettings::businessProfile();

        return [
            'phone' => $settings->phone,
            'phones' => $settings->phones,
            'email' => $settings->email,
            'address' => $settings->address,
            'city' => $settings->city,
        ];
    }

    /** @param array<string, mixed> $arguments
     *  @return array<string, mixed>
     */
    private function createSalesLead(array $arguments, AiConversation $conversation): array
    {
        if (! $conversation->privacy_accepted_at) {
            return ['created' => false, 'reason' => 'privacy_consent_required'];
        }

        if ($conversation->contact_lead_id) {
            return ['created' => true, 'lead_id' => $conversation->contact_lead_id, 'already_created' => true];
        }

        $phone = $this->normalizePhone((string) ($arguments['phone'] ?? ''));
        $phoneDigits = preg_replace('/\D+/', '', $phone) ?? '';
        $conversationDigits = preg_replace(
            '/\D+/',
            '',
            $conversation->messages()->where('role', 'user')->pluck('content')->implode(' '),
        ) ?? '';

        if ($phoneDigits === '' || strlen($phoneDigits) < 7 || ! str_contains($conversationDigits, $phoneDigits)) {
            return ['created' => false, 'reason' => 'phone_not_supplied_by_customer'];
        }

        $serviceSlug = trim((string) ($arguments['service_slug'] ?? ''));
        $service = $serviceSlug !== ''
            ? Service::query()->where('slug', $serviceSlug)->where('is_published', true)->first()
            : null;

        if ($serviceSlug !== '' && ! $service) {
            return ['created' => false, 'reason' => 'service_unavailable'];
        }

        $lead = $this->createLead->execute(new LeadData(
            name: $this->nullableString($arguments['name'] ?? null),
            firstName: null,
            lastName: null,
            company: null,
            phone: $phone,
            email: null,
            address: $this->nullableString($arguments['city'] ?? null),
            service: $service?->name,
            serviceSlug: $service?->slug,
            projectSize: null,
            propertyType: null,
            details: [[
                'key' => 'ai_conversation',
                'label' => 'AI conversation',
                'type' => 'text',
                'value' => $conversation->public_id,
            ]],
            message: $this->nullableString($arguments['message'] ?? null),
            source: 'ai-assistant',
            ipHash: (string) ($conversation->ip_hash ?: hash('sha256', $conversation->public_id)),
            userAgent: $conversation->user_agent,
        ));

        $conversation->forceFill([
            'contact_lead_id' => $lead->id,
            'lead_score' => 100,
            'status' => 'converted',
        ])->save();

        return ['created' => true, 'lead_id' => $lead->id];
    }

    /** @param array<int, array<string, mixed>> $toolsUsed */
    private function leadScore(AiConversation $conversation, array $toolsUsed): int
    {
        $text = $conversation->messages()->where('role', 'user')->pluck('content')->implode(' ');
        $score = 10;

        if (preg_match('/(?:\+?995)?[\s-]?5\d{2}[\s-]?\d{2}[\s-]?\d{2}[\s-]?\d{2}/u', $text)) {
            $score += 35;
        }

        if (collect($toolsUsed)->contains(fn (array $tool): bool => $tool['name'] === 'search_services')) {
            $score += 15;
        }

        if (collect($toolsUsed)->contains(fn (array $tool): bool => $tool['name'] === 'search_projects')) {
            $score += 10;
        }

        if (mb_strlen($text) > 120) {
            $score += 10;
        }

        return min(95, max((int) $conversation->lead_score, $score));
    }

    /** @param array<string, mixed> $payload
     *  @return array<string, mixed>
     */
    private function request(string $apiKey, array $payload): array
    {
        $response = $this->http($apiKey)->post('https://api.openai.com/v1/responses', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('OpenAI request failed with status '.$response->status().'.');
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw new RuntimeException('OpenAI returned an invalid response.');
        }

        return $json;
    }

    private function http(string $apiKey): PendingRequest
    {
        return Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->withHeaders(['X-Client-Request-Id' => (string) Str::uuid()])
            ->timeout(35)
            ->retry(1, 250, throw: false);
    }

    /** @param array<string, mixed> $response */
    private function extractText(array $response): string
    {
        foreach ($response['output'] ?? [] as $item) {
            if (! is_array($item) || ($item['type'] ?? null) !== 'message') {
                continue;
            }

            foreach ($item['content'] ?? [] as $content) {
                if (is_array($content) && ($content['type'] ?? null) === 'output_text') {
                    $text = trim((string) ($content['text'] ?? ''));

                    if ($text !== '') {
                        return $text;
                    }
                }
            }
        }

        return '';
    }

    /** @param array<string, mixed>|null $translations */
    private function localized(?array $translations, string $field, string $locale, string $fallback): string
    {
        $value = trim((string) data_get($translations ?? [], "fields.{$field}.{$locale}", ''));

        return $value !== '' ? $value : trim($fallback);
    }

    private function normalizePhone(string $phone): string
    {
        return trim((string) preg_replace('/[^+0-9]/', '', $phone));
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
