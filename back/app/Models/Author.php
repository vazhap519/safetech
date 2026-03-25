<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class Author extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'email',
        'socials',
    ];

    protected $casts = [
        'socials' => 'array',
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | MEDIA
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function getAvatarAttribute()
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getSocial($key)
    {
        return data_get($this->socials, $key);
    }

    /*
    |--------------------------------------------------------------------------
    | SLUG
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($author) {
            if (!$author->slug) {
                $slug = Str::slug($author->name);
                $count = self::where('slug', 'LIKE', "{$slug}%")->count();
                $author->slug = $count ? "{$slug}-{$count}" : $slug;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 300, 300)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('avatar')
            ->queued();
    }
}
