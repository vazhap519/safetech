<?php

namespace App\Domain\Content\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepository
{
    /** @param array<string, array<int, string>> $filters */
    public function allPublished(?string $category = null, array $filters = []): Collection;

    public function findPublishedBySlug(string $slug): ?Product;
}
