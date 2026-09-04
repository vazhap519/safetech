<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Re-seeds the public contact page without changing unrelated CMS content.
 *
 * Each delegated seeder only fills missing fields and honours canonical
 * deletion tombstones, so administrator edits and intentional deletions are
 * never overwritten or recreated.
 */
final class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        (new PageContentSeeder)->seedContactPageContent();
        (new SeoPageSeeder)->seedContactPage();
    }
}
