<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewInvitationSubmissionRequest;
use App\Models\ReviewInvitation;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReviewInvitationController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $invitation = ReviewInvitation::query()
            ->with('project')
            ->where('token', $token)
            ->first();

        $this->ensureAvailable($invitation);

        return response()->json([
            'data' => [
                'recipientName' => $invitation->recipient_name,
                'projectName' => $invitation->project?->name ?: $invitation->project?->title,
            ],
        ]);
    }

    public function submit(
        StoreReviewInvitationSubmissionRequest $request,
        string $token,
    ): JsonResponse {
        DB::transaction(function () use ($request, $token): void {
            $invitation = ReviewInvitation::query()
                ->where('token', $token)
                ->lockForUpdate()
                ->first();

            $this->ensureAvailable($invitation);

            $testimonial = Testimonial::query()->create([
                'quote' => $request->string('quote')->trim()->toString(),
                'author' => $request->string('author')->trim()->toString(),
                'company' => $request->string('company')->trim()->toString() ?: null,
                'role' => $request->string('role')->trim()->toString() ?: null,
                'is_active' => false,
            ]);

            $invitation->forceFill([
                'testimonial_id' => $testimonial->getKey(),
                'submitted_at' => now(),
                'consented_at' => now(),
                'is_active' => false,
            ])->save();
        });

        $message = match ($request->input('locale')) {
            'en' => 'Thank you. Your review was submitted for approval.',
            'ru' => 'Спасибо. Ваш отзыв отправлен на проверку.',
            default => 'გმადლობთ. თქვენი შეფასება გადაგზავნილია დასამტკიცებლად.',
        };

        return response()->json(['message' => $message], 201);
    }

    private function ensureAvailable(?ReviewInvitation $invitation): void
    {
        if (! $invitation) {
            abort(404, 'This review invitation could not be found.');
        }

        if ($invitation->submitted_at !== null) {
            abort(422, 'This review invitation has already been used.');
        }

        if (! $invitation->isAvailable()) {
            abort(404, 'This review invitation is unavailable or has expired.');
        }
    }
}
