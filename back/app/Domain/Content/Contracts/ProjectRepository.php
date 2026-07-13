<?php

namespace App\Domain\Content\Contracts;

use App\Models\Project;
use Illuminate\Support\Collection;

interface ProjectRepository
{
    public function allPublished(?bool $featured = null): Collection;
    public function findPublishedBySlug(string $slug): ?Project;
}
