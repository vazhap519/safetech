<?php

namespace Tests\Feature;

use App\Models\SeoPage;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\PageContentSeeder;
use Database\Seeders\SeoPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SevenServiceSeoCopyTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_acquisition_copy_matches_the_seven_service_catalog(): void
    {
        $this->seed(PageContentSeeder::class);
        $this->seed(SeoPageSeeder::class);

        (new SeoPageSeeder)->refreshCoreSeoPages();
        (new PageContentSeeder)->refreshSevenServiceCopy();

        $seoCopy = SeoPage::query()
            ->whereIn('key', ['home', 'services'])
            ->get()
            ->flatMap(fn (SeoPage $page): array => [
                $page->description,
                ...array_values((array) data_get($page->translations, 'fields.description', [])),
            ])
            ->implode(' ');

        $translations = MultilingualContent::mapFrom(
            SiteSetting::query()->where('key', 'translations')->firstOrFail()->value,
        );
        $pageCopy = collect([
            'home.hero.description',
            'about.who.item.2.description',
            'contact.support.item.4',
        ])->flatMap(fn (string $key): array => array_values($translations[$key] ?? []))->implode(' ');

        $this->assertStringNotContainsStringIgnoringCase('POS', $seoCopy);
        $this->assertStringNotContainsStringIgnoringCase('POS', $pageCopy);
        $this->assertStringContainsString('seven core services', $seoCopy);
        $this->assertStringContainsString('low-voltage cabling', $pageCopy);
    }
}
