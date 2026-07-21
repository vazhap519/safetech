<?php

namespace App\Filament\Support;

use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class StableSlug
{
    public static function syncOnCreate(): Closure
    {
        return function (
            ?string $state,
            ?string $old,
            Get $get,
            Set $set,
            ?Model $record,
        ): void {
            if ($record !== null) {
                return;
            }

            $currentSlug = trim((string) $get('slug'));
            $previousGeneratedSlug = Str::slug((string) $old);

            if ($currentSlug === '' || $currentSlug === $previousGeneratedSlug) {
                $set('slug', Str::slug((string) $state));
            }
        };
    }
}
