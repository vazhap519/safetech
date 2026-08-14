<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocalServiceLandingResource;
use App\Models\LocalServiceLanding;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class LocalServiceLandingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $serviceSlug = trim($request->string('service')->toString());
        $locationSlug = trim($request->string('location')->toString());

        $query = LocalServiceLanding::query()
            ->publiclyVisible()
            ->with(['service', 'publicProjects']);

        if ($serviceSlug !== '') {
            $query->whereHas(
                'service',
                fn (Builder $service): Builder => $service->where('slug', $serviceSlug),
            );
        }

        if ($locationSlug !== '') {
            $query->where('location_slug', $locationSlug);
        }

        return LocalServiceLandingResource::collection($query->get());
    }

    public function show(
        Request $request,
        string $service,
        string $location,
    ): LocalServiceLandingResource|JsonResponse {
        $landing = LocalServiceLanding::query()
            ->publiclyVisible()
            ->with(['service', 'publicProjects'])
            ->where('location_slug', $location)
            ->whereHas(
                'service',
                fn (Builder $query): Builder => $query->where('slug', $service),
            )
            ->first();

        if (! $landing) {
            return response()->json(['message' => 'Local service landing not found.'], 404);
        }

        return new LocalServiceLandingResource($landing);
    }
}
