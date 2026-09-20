<?php

namespace Tests\Feature;

use App\Application\Ai\SafeTechAiAgent;
use App\Models\AiKnowledgeItem;
use App\Models\SeedDeletionTombstone;
use Database\Seeders\AiKnowledgeBaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiKnowledgeBaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_substantial_reviewed_trilingual_knowledge_base_idempotently(): void
    {
        $this->assertCount(92, AiKnowledgeBaseSeeder::canonicalEntries());

        $this->seed(AiKnowledgeBaseSeeder::class);
        $this->seed(AiKnowledgeBaseSeeder::class);

        $this->assertDatabaseCount('ai_knowledge_items', 276);

        foreach (['ka', 'en', 'ru'] as $locale) {
            $this->assertSame(92, AiKnowledgeItem::query()->where('locale', $locale)->count());
            $this->assertSame(92, AiKnowledgeItem::query()->where('locale', $locale)
                ->where('status', 'approved')->where('source_type', 'curated')->count());
        }

        $this->assertGreaterThanOrEqual(8, AiKnowledgeItem::query()->distinct('category')->count('category'));
        $this->assertSame(276, AiKnowledgeItem::query()
            ->where('source_reference', 'like', AiKnowledgeBaseSeeder::REFERENCE_PREFIX.'%')
            ->distinct('source_reference')
            ->count('source_reference'));
        $this->assertTrue(AiKnowledgeItem::query()->get()
            ->every(fn (AiKnowledgeItem $item): bool => mb_strlen($item->content) >= 90));
    }

    public function test_it_respects_editor_changes_and_disabled_knowledge(): void
    {
        $this->seed(AiKnowledgeBaseSeeder::class);
        $item = AiKnowledgeItem::query()
            ->where('source_reference', 'safetech-kb:v1:camera-count:ka')
            ->sole();

        $item->update([
            'content' => 'დამტკიცებული ხელით შეცვლილი ტექსტი.',
            'status' => 'disabled',
            'usage_count' => 42,
        ]);

        $this->seed(AiKnowledgeBaseSeeder::class);
        $item->refresh();
        $this->assertSame('დამტკიცებული ხელით შეცვლილი ტექსტი.', $item->content);
        $this->assertSame('disabled', $item->status);
        $this->assertSame(42, $item->usage_count);
        $this->assertDatabaseCount('ai_knowledge_items', 276);
    }

    public function test_delete_is_remembered_and_not_recreated_on_next_deploy(): void
    {
        $this->seed(AiKnowledgeBaseSeeder::class);
        $item = AiKnowledgeItem::query()
            ->where('source_reference', 'safetech-kb:v1:camera-count:en')
            ->sole();
        $item->delete();

        $this->assertDatabaseHas('seed_deletion_tombstones', [
            'type' => 'ai-knowledge',
            'key' => 'safetech-kb:v1:camera-count:en',
        ]);

        $this->seed(AiKnowledgeBaseSeeder::class);

        $this->assertDatabaseCount('ai_knowledge_items', 275);
        $this->assertSame(0, AiKnowledgeItem::query()
            ->where('source_reference', 'safetech-kb:v1:camera-count:en')->count());
    }

    public function test_search_reaches_older_entries_after_more_than_150_rows_and_uses_language(): void
    {
        $this->seed(AiKnowledgeBaseSeeder::class);

        $results = $this->knowledgeSearch('HDD retention', 'en');
        $this->assertNotEmpty($results);
        $this->assertContains('en', array_column($results, 'locale'));
        $this->assertContains('ai', ['ai', 'kb']);
        $this->assertContains(
            'safetech-kb:v1:disk-retention:en',
            AiKnowledgeItem::query()
                ->whereIn('id', array_column($results, 'id'))
                ->pluck('source_reference')->all(),
        );

        $russian = $this->knowledgeSearch('Шлагбаум LPR', 'ru');
        $this->assertNotEmpty($russian);
        $this->assertContains('ru', array_column($russian, 'locale'));
        $this->assertTrue(AiKnowledgeItem::query()->where('usage_count', '>', 0)->exists());
    }

    public function test_unapproved_or_unknown_knowledge_is_not_used_by_customer_assistant(): void
    {
        AiKnowledgeItem::query()->create([
            'title' => 'Private integration key',
            'content' => 'secret-token-example-do-not-use',
            'category' => 'security',
            'locale' => 'en',
            'status' => 'disabled',
            'source_type' => 'manual',
        ]);

        $this->assertSame([], $this->knowledgeSearch('secret-token-example-do-not-use', 'en'));
    }

    /** @return array<int, array<string, mixed>> */
    private function knowledgeSearch(string $query, string $locale): array
    {
        return (new \ReflectionMethod(SafeTechAiAgent::class, 'searchKnowledge'))
            ->invoke(app(SafeTechAiAgent::class), $query, $locale);
    }
}
