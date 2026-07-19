<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCalculatorProfileResource;
use App\Models\Service;
use App\Support\Calculators\CalculatorProfileBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ServiceCalculatorProfileController extends Controller
{
    public function __invoke(
        Request $request,
        CalculatorProfileBuilder $builder,
    ): AnonymousResourceCollection {
        $serviceSlug = $request->string('service')->trim()->toString();

        $services = Service::query()
            ->published()
            ->when($serviceSlug !== '', fn ($query) => $query->where('slug', $serviceSlug))
            ->get()
            ->filter(fn (Service $service): bool => $builder->enabled($service))
            ->values();

        return ServiceCalculatorProfileResource::collection($services);
    }
}
