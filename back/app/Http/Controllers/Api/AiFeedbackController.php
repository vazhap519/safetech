<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiFeedback;
use App\Models\AiKnowledgeCandidate;
use App\Models\AiMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AiFeedbackController extends Controller
{
    public function __invoke(Request $request, string $message): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'in:-1,1'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $assistantMessage = AiMessage::query()
            ->where('public_id', $message)
            ->where('role', 'assistant')
            ->firstOrFail();

        $feedback = AiFeedback::query()->updateOrCreate(
            ['ai_message_id' => $assistantMessage->id],
            [
                'rating' => (int) $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'ip_hash' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
            ],
        );

        if ($feedback->rating === -1) {
            $question = AiMessage::query()
                ->where('ai_conversation_id', $assistantMessage->ai_conversation_id)
                ->where('role', 'user')
                ->where('id', '<', $assistantMessage->id)
                ->latest('id')
                ->value('content');

            if (filled($question)) {
                $candidate = AiKnowledgeCandidate::query()->firstOrCreate(
                    [
                        'question' => trim((string) $question),
                        'locale' => $assistantMessage->conversation?->locale ?? 'ka',
                        'status' => 'pending',
                    ],
                    [
                        'ai_conversation_id' => $assistantMessage->ai_conversation_id,
                        'suggested_answer' => $assistantMessage->content,
                        'occurrences' => 0,
                    ],
                );

                $candidate->increment('occurrences');
            }
        }

        return response()->json(['data' => ['saved' => true]]);
    }
}
