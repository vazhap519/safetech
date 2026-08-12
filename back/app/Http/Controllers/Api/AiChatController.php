<?php

namespace App\Http\Controllers\Api;

use App\Application\Ai\SafeTechAiAgent;
use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class AiChatController extends Controller
{
    public function __invoke(Request $request, SafeTechAiAgent $agent): JsonResponse
    {
        $validated = $request->validate([
            'conversation_id' => ['nullable', 'uuid'],
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'locale' => ['nullable', 'string', 'in:ka,en,ru'],
            'privacy' => ['required', 'accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        $locale = (string) ($validated['locale'] ?? 'ka');
        $ipHash = hash_hmac('sha256', (string) $request->ip(), (string) config('app.key'));
        $conversation = $this->resolveConversation(
            $validated['conversation_id'] ?? null,
            $locale,
            $ipHash,
            $request->userAgent(),
        );

        $conversation->messages()->create([
            'role' => 'user',
            'content' => trim((string) $validated['message']),
        ]);
        $conversation->forceFill([
            'locale' => $locale,
            'privacy_accepted_at' => $conversation->privacy_accepted_at ?: now(),
            'last_message_at' => now(),
        ])->save();

        try {
            $result = $agent->respond($conversation->fresh(), $locale);
        } catch (RuntimeException $exception) {
            Log::warning('SafeTech AI assistant request failed.', [
                'conversation_id' => $conversation->public_id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => $this->unavailableMessage($locale),
                'conversation_id' => $conversation->public_id,
            ], 503);
        }

        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $result['message'],
            'model' => $result['model'],
            'tool_payload' => $result['tools'],
            'input_tokens' => $result['input_tokens'],
            'output_tokens' => $result['output_tokens'],
        ]);

        $conversation->forceFill(['last_message_at' => now()])->save();

        return response()->json([
            'data' => [
                'conversation_id' => $conversation->public_id,
                'message_id' => $assistantMessage->public_id,
                'message' => $assistantMessage->content,
                'lead_score' => $result['lead_score'],
                'lead_created' => (bool) $conversation->fresh()->contact_lead_id,
            ],
        ]);
    }

    private function resolveConversation(?string $publicId, string $locale, string $ipHash, ?string $userAgent): AiConversation
    {
        if ($publicId) {
            $existing = AiConversation::query()->where('public_id', $publicId)->first();

            if ($existing) {
                return $existing;
            }
        }

        return AiConversation::query()->create([
            'locale' => $locale,
            'ip_hash' => $ipHash,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 1000) : null,
            'privacy_accepted_at' => now(),
            'last_message_at' => now(),
        ]);
    }

    private function unavailableMessage(string $locale): string
    {
        return match ($locale) {
            'en' => 'The AI consultant is temporarily unavailable. You can still contact SafeTech by phone or WhatsApp.',
            'ru' => 'AI-консультант временно недоступен. Вы можете связаться с SafeTech по телефону или WhatsApp.',
            default => 'AI კონსულტანტი დროებით მიუწვდომელია. SafeTech-ს შეგიძლიათ დაუკავშირდეთ ტელეფონით ან WhatsApp-ით.',
        };
    }
}
