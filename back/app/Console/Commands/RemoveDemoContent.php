<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveDemoContent extends Command
{
    protected $signature = 'cms:remove-demo-content
                            {--force : Kept for deployment compatibility}';

    protected $description = 'Confirm that bundled demo content is disabled';

    public function handle(): int
    {
        $this->info('No bundled demo content is configured.');

        return self::SUCCESS;
    }
}
