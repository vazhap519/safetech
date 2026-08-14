<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class LocalServiceLanding extends Model
{
    use FlushesPublicContentCache;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'faq' => 'array',
            'keywords' => 'array',
            'translations' => 'array',
            'is_published' => 'boolean',
            'noindex' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $landing): void {
            $landing->location_slug = Str::slug(
                (string) ($landing->location_slug ?: $landing->location_name),
            );
        });
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $published): void {
                $published
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereNotNull('location_slug')
            ->whereRaw("TRIM(COALESCE(location_slug, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(location_name, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(title, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(content, '')) <> ''")
            ->whereHas('service', fn (Builder $service): Builder => $service->publiclyVisible())
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'local_service_landing_project',
            'landing_id',
            'project_id',
        )->withTimestamps();
    }

    public function publicProjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'local_service_landing_project',
            'landing_id',
            'project_id',
        )
            ->where('projects.is_published', true)
            ->orderBy('projects.sort_order')
            ->withTimestamps();
    }
}
