<?php

namespace Tests\Feature;

use App\Jobs\RevalidateFrontend;
use App\Support\FrontendRevalidator;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FrontendRevalidatorTest extends TestCase
{
    public function test_revalidation_is_queued_instead_of_waiting_for_frontend_http(): void
    {
        Queue::fake();

        config()->set('app.frontend_url', 'https://safetech.example');
        config()->set('app.revalidate_secret', 'test-secret');

        FrontendRevalidator::revalidate('cms');

        Queue::assertPushed(
            RevalidateFrontend::class,
            fn (RevalidateFrontend $job): bool => $job->frontendUrl === 'https://safetech.example'
                && $job->secret === 'test-secret'
                && $job->tag === 'cms'
                && $job->path === null,
        );
    }

    public function test_revalidation_is_skipped_when_configuration_is_missing(): void
    {
        Queue::fake();

        config()->set('app.frontend_url', '');
        config()->set('app.revalidate_secret', '');

        FrontendRevalidator::revalidate('cms');

        Queue::assertNothingPushed();
    }
}
