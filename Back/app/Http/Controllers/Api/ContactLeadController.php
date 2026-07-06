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

        return response()->json([
            'message' => 'მადლობა! თქვენი მოთხოვნა წარმატებით გაიგზავნა.',
            'data' => ['id' => $lead->getKey(), 'status' => $lead->status],
        ], 201);
    }
}
