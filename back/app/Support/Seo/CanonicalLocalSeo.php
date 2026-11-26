<?php

namespace App\Support\Seo;

use App\Models\LocalServiceLanding;
use App\Support\Calculators\CanonicalServiceCalculator;
use Illuminate\Database\Eloquent\Builder;

final class CanonicalLocalSeo
{
    public const LOCATION_SLUGS = [
        'tbilisi',
        'bakuriani',
        'surami',
        'borjomi',
        'khashuri',
        'abastumani',
    ];

    /** @return array<int, string> */
    public static function serviceSlugs(): array
    {
        return CanonicalServiceCalculator::slugs();
    }

    /** @return array<int, string> */
    public static function locationSlugs(): array
    {
        return self::LOCATION_SLUGS;
    }

    public static function apply(Builder $query): Builder
    {
        return $query
            ->whereIn('location_slug', self::locationSlugs())
            ->whereHas(
                'service',
                fn (Builder $service): Builder => $service->whereIn('slug', self::serviceSlugs()),
            );
    }

    public static function prune(): int
    {
        $deleted = 0;

        LocalServiceLanding::query()
            ->where(function (Builder $obsolete): void {
                $obsolete
                    ->whereNotIn('location_slug', self::locationSlugs())
                    ->orWhereDoesntHave(
                        'service',
                        fn (Builder $service): Builder => $service->whereIn('slug', self::serviceSlugs()),
                    );
            })
            ->select('id')
            ->chunkById(100, function ($landings) use (&$deleted): void {
                foreach ($landings as $landing) {
                    $landing->delete();
                    $deleted++;
                }
            });

        return $deleted;
    }
}
