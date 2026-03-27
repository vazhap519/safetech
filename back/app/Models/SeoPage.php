<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SeoPage extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'key',
        'slug',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'canonical',
        'noindex',
    ];

    protected $casts = [
        'keywords' => 'array',
        'noindex' => 'boolean',
    ];

    protected $appends = [
        'og_image_url',
        'share_image_url',
        'meta',
    ];

    protected $attributes = [
        'keywords' => '[]',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function seoable()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO FIX + CACHE
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saving(function ($model) {

            if ($model->slug) {
                $model->slug = '/' . ltrim($model->slug, '/');
                $model->canonical = config('app.url') . $model->slug;
            }

            if (is_array($model->keywords)) {
                $model->keywords = collect($model->keywords)
                    ->map(fn ($k) => is_array($k) ? $k : ['value' => $k])
                    ->toArray();
            }
        });

        static::saved(fn ($model) => self::clearCache($model));
        static::deleted(fn ($model) => self::clearCache($model));
    }

    protected static function clearCache($model): void
    {
        if ($model->key) {
            Cache::forget("seo_{$model->key}");
        }

        if ($model->seoable_id && $model->seoable_type) {
            Cache::forget("seo_model_{$model->seoable_type}_{$model->seoable_id}");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA
    |--------------------------------------------------------------------------
    */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')->singleFile();
        $this->addMediaCollection('share_image')->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('og')
            ->width(1200)
            ->height(630);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getOgImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('og_image', 'og')
            ?: asset('default-og.jpg');
    }

    public function getShareImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('share_image', 'og')
            ?: $this->og_image_url;
    }

    public function getKeywordsAttribute($value)
    {
        $keywords = json_decode($value, true) ?? [];

        return collect($keywords)
            ->map(fn ($k) => is_array($k) ? $k : ['value' => $k])
            ->toArray();
    }

    public function getKeywordsListAttribute(): array
    {
        return collect($this->keywords)
            ->pluck('value')
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | META
    |--------------------------------------------------------------------------
    */
    public function getMetaAttribute(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords_list,

            'canonical' => $this->canonical,
            'robots' => $this->noindex ? 'noindex, nofollow' : 'index, follow',

            'og' => [
                'title' => $this->og_title ?: $this->title,
                'description' => $this->og_description ?: $this->description,
                'image' => $this->og_image_url,
            ],

            'share_image' => $this->share_image_url,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE HELPERS
    |--------------------------------------------------------------------------
    */
    public static function getByKey(string $key): ?self
    {
        return Cache::remember("seo_$key", 3600, fn () =>
        self::where('key', $key)->first()
        );
    }

    public static function resolve(string $key = null, $model = null): array
    {
        $seo = $key ? self::getByKey($key) : null;

        return $seo?->meta ?? [
            'title' => config('app.name'),
            'description' => '',
            'keywords' => [],
        ];
    }
}
