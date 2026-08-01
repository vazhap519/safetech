<?php

namespace Database\Seeders;

final class SystemContentSeeder extends ContentSeeder
{
    public function run(): void
    {
        $this->seedSystemContent();
        $this->call(ServiceCatalogSeeder::class);
    }
}
