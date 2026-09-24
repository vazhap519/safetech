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
            $currentSlug = trim((string) $get('slug'));
            $previousGeneratedSlug = self::fromTitle((string) $old);
            $recordSlug = trim((string) $record?->slug);

            if ($currentSlug === '' || $currentSlug === $previousGeneratedSlug || ($record !== null && $currentSlug === $recordSlug)) {
                $set('slug', self::fromTitle((string) $state));
            }
        };
    }

    /** Convert Georgian, Cyrillic, and Latin titles into usable URL slugs. */
    public static function fromTitle(string $title): string
    {
        $transliteration = [
            'ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e', 'ვ' => 'v', 'ზ' => 'z', 'თ' => 't', 'ი' => 'i', 'კ' => 'k', 'ლ' => 'l', 'მ' => 'm', 'ნ' => 'n', 'ო' => 'o', 'პ' => 'p', 'ჟ' => 'zh', 'რ' => 'r', 'ს' => 's', 'ტ' => 't', 'უ' => 'u', 'ფ' => 'p', 'ქ' => 'k', 'ღ' => 'gh', 'ყ' => 'q', 'შ' => 'sh', 'ჩ' => 'ch', 'ც' => 'ts', 'ძ' => 'dz', 'წ' => 'ts', 'ჭ' => 'ch', 'ხ' => 'kh', 'ჯ' => 'j', 'ჰ' => 'h',
        ];

        return Str::slug(strtr(mb_strtolower(trim($title)), $transliteration))
            ?: Str::slug($title)
            ?: 'item-'.substr(sha1(mb_strtolower(trim($title))), 0, 8);
    }
}
