<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category_id',
        'author_id',
        'reading_time',
        'published_year',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | MEDIA
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getImageAttribute(): ?string
    {
        try {
            $media = $this->getFirstMedia('cover');

            if (!$media) return null;

            // ✅ თუ webp არსებობს
            if ($media->hasGeneratedConversion('webp')) {
                return $media->getFullUrl('webp'); // 🔥 FIX
            }

            // ❗ fallback original image
            return $media->getFullUrl();

        } catch (\Throwable $e) {
            return null;
        }

    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function sections()
    {
        return $this->hasMany(PostSection::class)
            ->orderBy('position'); // ✅ FIXED
    }

    /*
    |--------------------------------------------------------------------------
    | SLUG GENERATION
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (!$post->slug) {
                $slug = Str::slug($post->title);

                $count = self::where('slug', 'LIKE', "{$slug}%")->count();

                $post->slug = $count
                    ? "{$slug}-{$count}"
                    : $slug;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA CONVERSIONS
    |--------------------------------------------------------------------------
    */

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('cover')
            ->nonQueued(); // 🔥 FIX
    }
}
