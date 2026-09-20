<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeItem;
use App\Models\SeedDeletionTombstone;
use Illuminate\Database\Seeder;

/**
 * Reviewed, general technical FAQ playbook for SafeTech's customer assistant.
 *
 * Safe to run on every deploy: never overwrite existing rows, unpublished
 * knowledge, usage counters or editor changes; respect deletion tombstones.
 */
final class AiKnowledgeBaseSeeder extends Seeder
{
    public const REFERENCE_PREFIX = 'safetech-kb:v1:';

    private const DATA_FILES = [
        'ai_knowledge_cctv.php',
        'ai_knowledge_network.php',
        'ai_knowledge_access.php',
        'ai_knowledge_it_sales.php',
    ];

    /** @return array<string, array<int, string>> */
    public static function canonicalEntries(): array
    {
        $entries = [];

        foreach (self::DATA_FILES as $file) {
            foreach (require database_path("seeders/data/{$file}") as $key => $row) {
                if (isset($entries[$key])) {
                    throw new \LogicException("Duplicate curated AI knowledge identifier: {$key}");
                }

                if (count($row) !== 7) {
                    throw new \LogicException("Invalid curated AI knowledge record: {$key}");
                }

                $entries[$key] = $row;
            }
        }

        return $entries;
    }

    public function run(): void
    {
        $existingReferences = AiKnowledgeItem::query()
            ->where('source_reference', 'like', self::REFERENCE_PREFIX.'%')
            ->pluck('source_reference')
            ->flip()
            ->all();
        $existingTitles = AiKnowledgeItem::query()
            ->get(['locale', 'title'])
            ->mapWithKeys(fn (AiKnowledgeItem $item): array => [
                "{$item->locale}|{$item->title}" => true,
            ])
            ->all();
        $deletedReferences = SeedDeletionTombstone::query()
            ->where('type', 'ai-knowledge')
            ->pluck('key')
            ->flip()
            ->all();

        foreach (self::canonicalEntries() as $slug => $row) {
            [$category, $kaTitle, $kaContent, $enTitle, $enContent, $ruTitle, $ruContent] = $row;

            foreach ([
                'ka' => [$kaTitle, $kaContent],
                'en' => [$enTitle, $enContent],
                'ru' => [$ruTitle, $ruContent],
            ] as $locale => [$title, $content]) {
                $reference = self::REFERENCE_PREFIX."{$slug}:{$locale}";

                if (
                    isset($existingReferences[$reference])
                    || isset($deletedReferences[$reference])
                    || isset($existingTitles["{$locale}|{$title}"])
                ) {
                    continue;
                }

                AiKnowledgeItem::query()->create([
                    'title' => $title,
                    'content' => $content,
                    'category' => $category,
                    'locale' => $locale,
                    'status' => 'approved',
                    'source_type' => 'curated',
                    'source_reference' => $reference,
                    'usage_count' => 0,
                ]);
                $existingReferences[$reference] = true;
                $existingTitles["{$locale}|{$title}"] = true;
            }
        }
    }
}
