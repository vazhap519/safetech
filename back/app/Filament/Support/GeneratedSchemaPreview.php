<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

final class GeneratedSchemaPreview
{
    public static function service(): Placeholder
    {
        return self::placeholder('service', fn (Get $get): array => self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $get('translations.fields.seoTitle.ka') ?: $get('title') ?: $get('name'),
            'description' => $get('seo_description') ?: $get('description'),
            'url' => self::url('/services/'.trim((string) $get('slug'), '/')),
            'provider' => self::organization(),
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Georgia',
            ],
            'keywords' => self::keywords($get('keywords')),
        ]));
    }

    public static function project(): Placeholder
    {
        return self::placeholder('project', fn (Get $get): array => self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $get('translations.fields.seoTitle.ka') ?: $get('title') ?: $get('name'),
            'description' => $get('seo_description') ?: $get('description'),
            'url' => self::url('/projects/'.trim((string) $get('slug'), '/')),
            'creator' => self::organization(),
            'locationCreated' => filled($get('city')) ? [
                '@type' => 'Place',
                'name' => $get('city'),
            ] : null,
            'about' => filled($get('object_type')) ? [
                '@type' => 'Thing',
                'name' => $get('object_type'),
            ] : null,
            'mentions' => collect(is_array($get('equipment')) ? $get('equipment') : [])
                ->map(fn (array $item): array => self::clean([
                    '@type' => 'Product',
                    'name' => $item['name'] ?? null,
                    'model' => $item['model'] ?? null,
                    'description' => $item['quantity'] ?? null,
                ]))
                ->filter(fn (array $item): bool => filled($item['name'] ?? null))
                ->values()
                ->all(),
            'keywords' => self::keywords($get('seo.keywords')),
        ]));
    }

    public static function category(string $kind): Placeholder
    {
        $prefix = $kind === 'project' ? '/projects/category/' : '/services/category/';

        return self::placeholder("{$kind}-category", fn (Get $get): array => self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $get('seo_title') ?: $get('name'),
            'description' => $get('seo_description') ?: strip_tags((string) $get('intro_text')),
            'url' => self::url($prefix.trim((string) $get('slug'), '/')),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'SafeTech',
                'url' => self::url('/'),
            ],
            'keywords' => self::keywords($get('seo_keywords')),
        ]));
    }

    public static function localService(): Placeholder
    {
        return self::placeholder('local-service', fn (Get $get): array => self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $get('seo_title') ?: $get('title'),
            'description' => $get('seo_description') ?: $get('excerpt') ?: $get('content'),
            'provider' => self::organization(),
            'areaServed' => filled($get('location_name')) ? [
                '@type' => 'City',
                'name' => $get('location_name'),
            ] : null,
            'keywords' => self::keywords($get('keywords')),
            'mainEntity' => self::faq($get('faq')),
        ]));
    }

    public static function page(): Placeholder
    {
        return self::placeholder('page', fn (Get $get): array => self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $get('seo_title') ?: $get('title'),
            'description' => $get('seo_description') ?: $get('excerpt'),
            'url' => self::url('/'.trim((string) $get('slug'), '/')),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'SafeTech',
                'url' => self::url('/'),
            ],
            'keywords' => self::keywords($get('keywords')),
        ]));
    }

    private static function placeholder(string $key, callable $builder): Placeholder
    {
        return Placeholder::make("generated_schema_preview_{$key}")
            ->label('ავტომატურად გენერირებული Schema JSON-LD')
            ->content(function (Get $get) use ($builder): HtmlString {
                $json = json_encode(
                    $builder($get),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                ) ?: '{}';

                return new HtmlString(
                    '<div class="rounded-xl bg-gray-950 p-4 text-xs leading-5 text-gray-100 overflow-x-auto">'.
                    '<pre class="whitespace-pre-wrap">'.e($json).'</pre></div>'.
                    '<p class="mt-2 text-xs text-gray-500">Preview ავტომატურად იცვლება ფორმის მონაცემების მიხედვით. საბოლოო frontend schema შეიძლება დამატებით შეიცავდეს საიტის, მედიისა და დაკავშირებული ჩანაწერების მონაცემებს.</p>',
                );
            })
            ->columnSpanFull();
    }

    private static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => self::url('/').'#organization',
            'name' => 'SafeTech',
            'url' => self::url('/'),
        ];
    }

    private static function keywords(mixed $value): ?string
    {
        $items = collect(is_array($value) ? $value : [])
            ->map(fn (mixed $item): string => trim((string) (is_array($item) ? ($item['value'] ?? '') : $item)))
            ->filter()
            ->unique()
            ->values();

        return $items->isEmpty() ? null : $items->implode(', ');
    }

    private static function faq(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(fn (array $item): array => self::clean([
                '@type' => 'Question',
                'name' => $item['question'] ?? null,
                'acceptedAnswer' => filled($item['answer'] ?? null) ? [
                    '@type' => 'Answer',
                    'text' => strip_tags((string) $item['answer']),
                ] : null,
            ]))
            ->filter(fn (array $item): bool => filled($item['name'] ?? null))
            ->values()
            ->all();
    }

    private static function clean(array $value): array
    {
        return collect($value)
            ->reject(fn (mixed $item): bool => $item === null || $item === '' || $item === [])
            ->map(fn (mixed $item): mixed => is_array($item) && Arr::isAssoc($item) ? self::clean($item) : $item)
            ->all();
    }

    private static function url(string $path): string
    {
        $path = '/'.ltrim($path, '/');

        return 'https://safetech.ge'.($path === '/' ? '/' : rtrim($path, '/'));
    }
}
