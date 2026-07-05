<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Contracts\ServiceRepository;
use App\Models\Service;
use App\Support\PublicContentCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class EloquentServiceRepository implements ServiceRepository
{
    public function allPublished(): Collection
    {
        return Cache::remember(PublicContentCache::key('services'), now()->addHour(), fn () =>
            Service::query()->published()->with(['faqs' => fn ($query) => $query->active()])->get()
        );
    }

    public function findPublishedBySlug(string $slug): ?Service
    {
        return Cache::remember(PublicContentCache::key('service:'.$slug), now()->addHour(), fn () =>
            Service::query()->published()->with(['faqs' => fn ($query) => $query->active()])->where('slug', $slug)->first()
        );
    }
}
