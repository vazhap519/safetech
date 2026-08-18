<?php

namespace Tests\Unit;

use App\Support\FrontendRevalidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FrontendRevalidatorTest extends TestCase
{
    public function test_it_sends_the_expected_revalidation_payload(): void
    {
        config()->set('app.frontend_url', 'https://safetech.ge');
        config()->set('app.revalidate_secret', 'test-secret');
        Http::fake([
            'https://safetech.ge/api/revalidate' => Http::response(['success' => true], 200),
        ]);

        FrontendRevalidator::revalidate('cms', '/services');

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://safetech.ge/api/revalidate'
                && $request->hasHeader('x-secret', 'test-secret')
                && $request['tag'] === 'cms'
                && $request['path'] === '/services';
        });
    }

    public function test_it_logs_unsuccessful_revalidation_without_throwing(): void
    {
        config()->set('app.frontend_url', 'https://safetech.ge');
        config()->set('app.revalidate_secret', 'test-secret');
        Http::fake([
            'https://safetech.ge/api/revalidate' => Http::response(['success' => false], 401),
        ]);
        Log::spy();

        FrontendRevalidator::revalidate('cms');

        Log::shouldHaveReceived('warning')
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'Frontend cache revalidation request failed.'
                    && $context['status'] === 401
                    && $context['tag'] === 'cms';
            })
            ->times(3);
    }
}
