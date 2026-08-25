<?php

namespace App\Http\Controllers\Api;

use App\Application\Leads\Actions\CreateLead;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactLeadRequest;
use App\Models\ContactLead;
use Illuminate\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

final class ContactLeadController extends Controller
{
    public function __invoke(StoreContactLeadRequest $request, CreateLead $action): JsonResponse
    {
        $idempotencyKey = trim((string) $request->header('Idempotency-Key'));

        if ($idempotencyKey !== '' && preg_match('/^[A-Za-z0-9._:-]{8,128}$/', $idempotencyKey) !== 1) {
            return response()->json([
                'message' => 'Invalid Idempotency-Key header.',
            ], 422);
        }

        try {
            [$lead, $created] = $idempotencyKey === ''
                ? [$action->execute($request->toData()), true]
                : $this->createIdempotently($idempotencyKey, $request, $action);
        } catch (LockTimeoutException) {
            return response()->json([
                'message' => match ($request->input('locale')) {
                    'en' => 'Your request is still being processed. Please try again.',
                    'ru' => 'Ваша заявка ещё обрабатывается. Пожалуйста, повторите попытку.',
                    default => 'თქვენი მოთხოვნა ჯერ კიდევ მუშავდება. გთხოვთ, სცადოთ ხელახლა.',
                },
            ], 409);
        }

        $message = match ($request->input('locale')) {
            'en' => 'Thank you. Your request was sent successfully.',
            'ru' => 'Спасибо. Ваша заявка успешно отправлена.',
            default => 'მადლობა! თქვენი მოთხოვნა წარმატებით გაიგზავნა.',
        };

        return response()->json([
            'message' => $message,
            'data' => [
                'id' => $lead->getKey(),
                'status' => $lead->status,
                'replayed' => ! $created,
            ],
        ], $created ? 201 : 200);
    }

    /** @return array{ContactLead, bool} */
    private function createIdempotently(
        string $idempotencyKey,
        StoreContactLeadRequest $request,
        CreateLead $action,
    ): array {
        $cacheKey = 'contact-lead:idempotency:'.hash('sha256', $idempotencyKey);

        return Cache::lock("{$cacheKey}:lock", 15)->block(
            10,
            function () use ($cacheKey, $request, $action): array {
                $existingId = Cache::get($cacheKey);

                if ($existingId !== null) {
                    $existingLead = ContactLead::query()->find($existingId);

                    if ($existingLead) {
                        return [$existingLead, false];
                    }
                }

                $lead = $action->execute($request->toData());
                Cache::put($cacheKey, $lead->getKey(), now()->addHours(6));

                return [$lead, true];
            },
        );
    }
}
