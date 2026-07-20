<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Author extends Model implements HasMedia
{
    use FlushesPublicContentCache, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'email',
        'socials',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'socials' => 'array',
            'translations' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Author $author): void {
            if ($author->slug) {
                return;
            }

            $baseSlug = Str::slug($author->name) ?: Str::lower(Str::random(10));
            $candidate = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $candidate)->exists()) {
                $candidate = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $author->slug = $candidate;
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getSocial(string $key): mixed
    {
        return data_get($this->socials, $key);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('public')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 300, 300)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('avatar')
            ->nonQueued();
    }

    public function getAvatarAttribute(): ?string
    {
        $media = $this->getFirstMedia('avatar');

        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('webp')
            ? $media->getUrl('webp')
            : $media->getUrl();
    }
}
