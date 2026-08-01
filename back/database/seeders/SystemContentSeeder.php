<?php

namespace Database\Seeders;

final class SystemContentSeeder extends ContentSeeder
{
    public function run(): void
    {
        $this->seedSystemContent();
        $this->call(PageContentSeeder::class);
        $this->call(ServiceCatalogSeeder::class);
    }
}
