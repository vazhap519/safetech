<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCalculatorProfileResource;
use App\Models\Service;
use App\Support\Calculators\CalculatorProfileBuilder;
use App\Support\Calculators\DefaultCalculatorProfiles;
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

        if ($services->isEmpty() && ! Service::query()->published()->exists()) {
            $names = [
                'cctv' => ['ვიდეოსამეთვალყურეობა', 'CCTV', 'Видеонаблюдение'],
                'networking' => ['ქსელური ინფრასტრუქტურა', 'Networking', 'Сетевая инфраструктура'],
                'access-control' => ['დაშვების კონტროლი', 'Access control', 'Контроль доступа'],
                'server-infrastructure' => ['სერვერული ინფრასტრუქტურა', 'Server infrastructure', 'Серверная инфраструктура'],
                'it-support' => ['IT მხარდაჭერა', 'IT support', 'IT-поддержка'],
            ];

            $services = collect(DefaultCalculatorProfiles::all())
                ->when(
                    $serviceSlug !== '',
                    fn ($profiles) => $profiles->only($serviceSlug),
                )
                ->map(function (array $profile, string $slug) use ($names): Service {
                    [$ka, $en, $ru] = $names[$slug] ?? [$slug, $slug, $slug];

                    return new Service([
                        'slug' => $slug,
                        'name' => $ka,
                        'title' => $ka,
                        'description' => '',
                        'icon' => 'settings',
                        'lead_form' => $profile,
                        'translations' => [
                            'fields' => [
                                'name' => compact('ka', 'en', 'ru'),
                            ],
                        ],
                    ]);
                })
                ->values();
        }

        return ServiceCalculatorProfileResource::collection($services);
    }
}
