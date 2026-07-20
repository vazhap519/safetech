<?php

namespace App\Http\Controllers\Api;

use App\Application\Leads\Actions\CreateLead;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactLeadRequest;
use Illuminate\Http\JsonResponse;

final class ContactLeadController extends Controller
{
    public function __invoke(StoreContactLeadRequest $request, CreateLead $action): JsonResponse
    {
        $lead = $action->execute($request->toData());
        $message = match ($request->input('locale')) {
            'en' => 'Thank you. Your request was sent successfully.',
            'ru' => 'Спасибо. Ваша заявка успешно отправлена.',
            default => 'მადლობა! თქვენი მოთხოვნა წარმატებით გაიგზავნა.',
        };

        return response()->json([
            'message' => $message,
            'data' => ['id' => $lead->getKey(), 'status' => $lead->status],
        ], 201);
    }
}
