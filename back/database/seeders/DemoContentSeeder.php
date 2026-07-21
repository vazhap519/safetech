<?php

namespace Database\Seeders;

final class DemoContentSeeder extends ContentSeeder
{
    public function run(): void
    {
        if (app()->environment('production') || ! config('app.seed_demo_content')) {
            return;
        }

        $this->seedDemoContent();
    }
}
