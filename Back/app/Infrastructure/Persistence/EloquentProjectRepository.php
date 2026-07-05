<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Models\Project;
use App\Support\PublicContentCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class EloquentProjectRepository implements ProjectRepository
{
    public function allPublished(?bool $featured = null): Collection
    {
        $suffix = $featured === null ? 'all' : ($featured ? 'featured' : 'standard');

        return Cache::remember(PublicContentCache::key('projects:'.$suffix), now()->addHour(), function () use ($featured) {
            return Project::query()->published()->when($featured !== null, fn ($query) => $query->where('is_featured', $featured))->get();
        });
    }

    public function findPublishedBySlug(string $slug): ?Project
    {
        return Cache::remember(PublicContentCache::key('project:'.$slug), now()->addHour(), fn () =>
            Project::query()->published()->where('slug', $slug)->first()
        );
    }
}
