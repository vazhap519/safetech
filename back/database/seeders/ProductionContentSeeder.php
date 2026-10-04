<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Single source of truth for public SafeTech content.
 *
 * Projects and project categories are intentionally excluded: they are
 * administrator-owned records and must survive SEO/service rebuilds.
 */
final class ProductionContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seed(rebuildServices: false);
    }

    public function rebuild(): void
    {
        $this->seed(rebuildServices: true);
    }

    private function seed(bool $rebuildServices): void
    {
        $this->call(ContentSeeder::class);
        $this->call(PageContentSeeder::class);
        $this->call(ConsultationCopySeeder::class);
        $this->call(PrivacyPageSeeder::class);

        if ($rebuildServices) {
            app(ServiceCatalogSeeder::class)->rebuild();
        } else {
            $this->call(ServiceCatalogSeeder::class);
        }

        $this->call(IntercomConfiguratorSeeder::class);
        $this->call(QuoteCatalogSeeder::class);
        $this->call(CanonicalLocalSeoSeeder::class);
        $this->call(SeoPageSeeder::class);
        $this->call(AiKnowledgeBaseSeeder::class);
    }
}
