<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CameraPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

final class CameraPlanController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:140'],
            'contact_name' => ['required', 'string', 'min:2', 'max:100'],
            'contact_phone' => ['required', 'string', 'regex:/^[+()0-9\s-]{7,24}$/'],
            'contact_email' => ['nullable', 'email', 'max:160'],
            'privacy' => ['accepted'],
            'website' => ['nullable', 'max:0'],
            'layout' => ['required', 'string', 'json', 'max:110000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        $layout = json_decode($validated['layout'], true, 32, JSON_THROW_ON_ERROR);
        $rules = [
            'version' => ['required', 'integer', 'in:1'],
            'widthMeters' => ['required', 'numeric', 'between:1,500'],
            'cameras' => ['present', 'array', 'min:1', 'max:128'],
            'walls' => ['present', 'array', 'max:256'],
            'area' => ['present', 'array', 'max:80'],
            'cameras.*.x' => ['required', 'numeric', 'between:0,900'],
            'cameras.*.y' => ['required', 'numeric', 'between:0,600'],
            'cameras.*.direction' => ['required', 'numeric', 'between:0,360'],
            'cameras.*.fov' => ['required', 'numeric', 'between:15,180'],
            'cameras.*.range' => ['required', 'numeric', 'between:1,100'],
            'cameras.*.kind' => ['required', 'string', 'in:bullet,dome,ptz,turret'],
            'walls.*.ax' => ['required', 'numeric', 'between:0,900'],
            'walls.*.ay' => ['required', 'numeric', 'between:0,600'],
            'walls.*.bx' => ['required', 'numeric', 'between:0,900'],
            'walls.*.by' => ['required', 'numeric', 'between:0,600'],
            'area.*.x' => ['required', 'numeric', 'between:0,900'],
            'area.*.y' => ['required', 'numeric', 'between:0,600'],
        ];

        if (! is_array($layout)) {
            throw ValidationException::withMessages(['layout' => 'Invalid planner layout.']);
        }

        $layout = Validator::make($layout, $rules)->validate();
        $backgroundPath = $request->file('image')?->store('camera-plans', 'local');

        try {
            $plan = CameraPlan::create([
                'title' => $validated['title'],
                'contact_name' => $validated['contact_name'],
                'contact_phone' => $validated['contact_phone'],
                'contact_email' => $validated['contact_email'] ?? null,
                'layout' => $layout,
                'background_path' => $backgroundPath,
                'privacy_accepted_at' => now(),
            ]);
        } catch (Throwable $exception) {
            if ($backgroundPath) {
                Storage::disk('local')->delete($backgroundPath);
            }
            throw $exception;
        }

        return response()->json([
            'message' => 'გეგმა მიღებულია. SafeTech დაგიკავშირდებათ.',
            'data' => ['id' => $plan->getKey()],
        ], 201);
    }
}
