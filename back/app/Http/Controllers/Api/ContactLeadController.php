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
            [$lead, $created, $conflict] = $idempotencyKey === ''
                ? [$action->execute($request->toData()), true, false]
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

        if ($conflict || ! $lead instanceof ContactLead) {
            return response()->json([
                'message' => match ($request->input('locale')) {
                    'en' => 'This submission key was already used for different request data. Please submit again.',
                    'ru' => 'Этот ключ отправки уже использован для других данных. Пожалуйста, отправьте форму ещё раз.',
                    default => 'ეს გაგზავნის გასაღები უკვე გამოყენებულია სხვა მონაცემებისთვის. გთხოვთ, ფორმა თავიდან გაგზავნოთ.',
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

    /** @return array{ContactLead|null, bool, bool} */
    private function createIdempotently(
        string $idempotencyKey,
        StoreContactLeadRequest $request,
        CreateLead $action,
    ): array {
        $cacheKey = 'contact-lead:idempotency:'.hash('sha256', $idempotencyKey);
        $payloadHash = hash(
            'sha256',
            json_encode(
                $request->validated(),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            ),
        );

        return Cache::lock("{$cacheKey}:lock", 15)->block(
            10,
            function () use ($cacheKey, $payloadHash, $request, $action): array {
                $existing = Cache::get($cacheKey);

                if (is_array($existing) && isset($existing['lead_id'])) {
                    if (
                        isset($existing['payload_hash'])
                        && ! hash_equals((string) $existing['payload_hash'], $payloadHash)
                    ) {
                        return [null, false, true];
                    }

                    $existingLead = ContactLead::query()->find($existing['lead_id']);

                    if ($existingLead) {
                        return [$existingLead, false, false];
                    }
                } elseif (is_int($existing) || ctype_digit((string) $existing)) {
                    // Backward compatibility for a key written by the first
                    // idempotency implementation before payload hashes existed.
                    $existingLead = ContactLead::query()->find((int) $existing);

                    if ($existingLead) {
                        return [$existingLead, false, false];
                    }
                }

                $lead = $action->execute($request->toData());
                Cache::put($cacheKey, [
                    'lead_id' => $lead->getKey(),
                    'payload_hash' => $payloadHash,
                ], now()->addHours(6));

                return [$lead, true, false];
            },
        );
    }
}
