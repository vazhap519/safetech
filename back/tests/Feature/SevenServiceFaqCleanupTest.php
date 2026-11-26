<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Service;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SevenServiceFaqCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_seeding_removes_faqs_for_retired_services(): void
    {
        $legacyService = Service::query()->create([
            'slug' => 'custom-computer-build',
            'name' => 'Legacy service',
            'title' => 'Legacy service',
            'description' => 'No longer publicly offered.',
            'is_published' => false,
        ]);

        $legacyFaq = Faq::query()->create([
            'service_id' => $legacyService->id,
            'context' => 'service:custom-computer-build:parts',
            'question' => 'Legacy question?',
            'answer' => 'Legacy answer.',
            'is_active' => true,
        ]);

        $this->seed(ServiceCatalogSeeder::class);

        $this->assertDatabaseMissing('faqs', ['id' => $legacyFaq->id]);
        $this->assertSame(
            [],
            Faq::query()
                ->where('context', 'like', 'service:%')
                ->whereNotIn('context', ServiceCatalogSeeder::canonicalFaqContexts())
                ->pluck('context')
                ->all(),
        );
    }
}
