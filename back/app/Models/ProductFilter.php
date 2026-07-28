<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductFilter extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'name',
        'slug',
        'options',
        'sort_order',
        'translations',
    ];

    protected $casts = [
        'options' => 'array',
        'translations' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductFilter $filter): void {
            if (filled($filter->slug)) {
                return;
            }

            $baseSlug = Str::slug($filter->name ?: 'product-filter') ?: 'product-filter';
            $candidate = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $candidate)->exists()) {
                $candidate = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $filter->slug = $candidate;
        });
    }

    /** @return Collection<int, array<string, mixed>> */
    public function resolvedOptions(): Collection
    {
        return collect($this->options ?? [])
            ->map(function (mixed $option): ?array {
                if (! is_array($option)) {
                    return null;
                }

                $label = trim((string) ($option['label'] ?? $option['name'] ?? ''));
                $slug = trim((string) ($option['slug'] ?? ''));

                if ($label === '') {
                    return null;
                }

                if ($slug === '') {
                    $slug = Str::slug($label) ?: Str::slug($this->name.'-'.$label);
                }

                return [
                    'label' => $label,
                    'slug' => $slug,
                    'sort_order' => (int) ($option['sort_order'] ?? 0),
                    'translations' => is_array($option['translations'] ?? null)
                        ? $option['translations']
                        : [
                            'ka' => is_string($option['ka'] ?? null) ? $option['ka'] : '',
                            'en' => is_string($option['en'] ?? null) ? $option['en'] : '',
                            'ru' => is_string($option['ru'] ?? null) ? $option['ru'] : '',
                        ],
                ];
            })
            ->filter()
            ->sortBy([
                ['sort_order', 'asc'],
                ['label', 'asc'],
            ])
            ->values();
    }
}
