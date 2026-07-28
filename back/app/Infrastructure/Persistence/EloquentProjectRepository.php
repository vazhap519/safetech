<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Models\Project;
use Illuminate\Support\Collection;

final class EloquentProjectRepository implements ProjectRepository
{
    public function allPublished(?bool $featured = null, ?string $category = null): Collection
    {
        return Project::query()
            ->publiclyVisible()
            ->with(['projectCategory', 'media'])
            ->when($featured !== null, fn ($query) => $query->where('is_featured', $featured))
            ->when(
                $category && $category !== 'all',
                fn ($query) => $query->whereHas(
                    'projectCategory',
                    fn ($categoryQuery) => $categoryQuery->where('slug', $category),
                ),
            )
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Project
    {
        return Project::query()
            ->publiclyVisible()
            ->with(['projectCategory', 'media'])
            ->where('slug', $slug)
            ->first();
    }
}
