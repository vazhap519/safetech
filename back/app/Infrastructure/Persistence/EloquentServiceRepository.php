<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Contracts\ServiceRepository;
use App\Models\Service;
use Illuminate\Support\Collection;

final class EloquentServiceRepository implements ServiceRepository
{
    public function allPublished(?string $category = null): Collection
    {
        return Service::query()
            ->published()
            ->with(['category', 'media', 'faqs' => fn ($query) => $query->active()])
            ->when(
                $category && $category !== 'all',
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('slug', $category),
                ),
            )
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Service
    {
        return Service::query()
            ->published()
            ->with(['category', 'media', 'faqs' => fn ($query) => $query->active()])
            ->where('slug', $slug)
            ->first();
    }
}
