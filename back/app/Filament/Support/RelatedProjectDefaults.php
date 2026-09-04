<?php

namespace App\Filament\Support;

use App\Models\Project;
use App\Support\MultilingualContent;

/**
 * Builds the editable card overrides for a project selected in the
 * ProjectResource "Related projects" repeater.
 */
final class RelatedProjectDefaults
{
    /**
     * A blank selection clears generated values. An unknown selection returns
     * null so an editor's existing overrides are not lost unexpectedly.
     *
     * @return array<string, string|null>|null
     */
    public static function forSlug(?string $slug): ?array
    {
        $slug = trim((string) $slug);

        if ($slug === '') {
            return self::emptyState();
        }

        $project = Project::query()
            ->with('projectCategory')
            ->where('slug', $slug)
            ->first();

        if (! $project) {
            return null;
        }

        return self::fromProject($project);
    }

    /**
     * @return array<string, string|null>
     */
    public static function fromProject(Project $project): array
    {
        $titleFallback = trim((string) ($project->title ?: $project->name));
        $title = MultilingualContent::valuesForField($project, 'title', $titleFallback);

        $category = $project->relationLoaded('projectCategory')
            ? $project->getRelation('projectCategory')
            : $project->projectCategory;
        $categoryValues = $category
            ? MultilingualContent::valuesForField($category, 'name', $category->name)
            : self::fallbackValues((string) $project->category_name);

        $imageAltFallback = trim((string) ($project->image_alt ?: $titleFallback));
        $imageAlt = MultilingualContent::valuesForField($project, 'imageAlt', $imageAltFallback);

        return [
            'title' => $title['ka'],
            'translations.en.title' => $title['en'],
            'translations.ru.title' => $title['ru'],
            'category' => $categoryValues['ka'],
            'translations.en.category' => $categoryValues['en'],
            'translations.ru.category' => $categoryValues['ru'],
            'imageAlt' => $imageAlt['ka'],
            'translations.en.imageAlt' => $imageAlt['en'],
            'translations.ru.imageAlt' => $imageAlt['ru'],
        ];
    }

    /**
     * @return array<string, null>
     */
    private static function emptyState(): array
    {
        return array_fill_keys([
            'title',
            'translations.en.title',
            'translations.ru.title',
            'category',
            'translations.en.category',
            'translations.ru.category',
            'imageAlt',
            'translations.en.imageAlt',
            'translations.ru.imageAlt',
        ], null);
    }

    /**
     * @return array{ka: string, en: string, ru: string}
     */
    private static function fallbackValues(string $value): array
    {
        return [
            'ka' => trim($value),
            'en' => '',
            'ru' => '',
        ];
    }
}
