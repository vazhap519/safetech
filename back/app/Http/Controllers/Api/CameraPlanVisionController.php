<?php

namespace App\Http\Controllers\Api;

use App\Application\Ai\CameraPlanVisionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

final class CameraPlanVisionController extends Controller
{
    public function __invoke(Request $request, CameraPlanVisionService $vision): JsonResponse
    {
        // Paid vision calls are opt-in and can be independently disabled from chatbot.
        abort_unless((bool) config('services.openai.planner_enabled', false), 503);

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
            'width_meters' => ['required', 'numeric', 'between:1,500'],
            'scale_confirmed' => ['required', 'boolean'],
            'locale' => ['required', 'in:ka,en,ru'],
            'ai_consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        try {
            $draft = $vision->propose(
                $request->file('image'),
                (float) $validated['width_meters'],
                (bool) $validated['scale_confirmed'],
                $validated['locale'],
            );
        } catch (ValidationException $exception) {
            // Invalid vendor geometry should never be leaked to the customer as a valid plan.
            Log::warning('Camera plan vision produced invalid geometry.', [
                'errors' => array_keys($exception->errors()),
            ]);

            return response()->json(['message' => 'AI plan needs manual review. Please try a clearer drawing.'], 422);
        } catch (RuntimeException|ConnectionException $exception) {
            Log::warning('Camera plan vision unavailable.', [
                'type' => class_basename($exception),
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'AI planning is temporarily unavailable. Manual planning still works.'], 503);
        }

        return response()->json(['data' => $draft], 200)
            ->header('Cache-Control', 'no-store, private');
    }
}
