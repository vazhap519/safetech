<?php

namespace App\Domain\Content\Contracts;

use App\Models\Service;
use Illuminate\Support\Collection;

interface ServiceRepository
{
    public function allPublished(?string $category = null): Collection;
    public function findPublishedBySlug(string $slug): ?Service;
}
