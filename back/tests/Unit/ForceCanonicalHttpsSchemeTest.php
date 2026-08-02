<?php

namespace Tests\Unit;

use App\Http\Middleware\ForceCanonicalHttpsScheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ForceCanonicalHttpsSchemeTest extends TestCase
{
    public function test_https_app_url_forces_the_request_scheme_before_signed_routes_are_checked(): void
    {
        config()->set('app.url', 'https://api.safetech.ge');

        $request = Request::create(
            'http://api.safetech.ge/livewire/upload-file?expires=123&signature=test',
            'POST',
        );

        $response = (new ForceCanonicalHttpsScheme)->handle(
            $request,
            fn (Request $handledRequest): JsonResponse => response()->json([
                'root' => $handledRequest->root(),
                'url' => $handledRequest->url(),
                'secure' => $handledRequest->secure(),
            ]),
        );

        $this->assertSame('https://api.safetech.ge', $response->getData(true)['root']);
        $this->assertSame(
            'https://api.safetech.ge/livewire/upload-file',
            $response->getData(true)['url'],
        );
        $this->assertTrue($response->getData(true)['secure']);
    }

    public function test_http_app_url_does_not_rewrite_a_local_request(): void
    {
        config()->set('app.url', 'http://localhost');

        $request = Request::create('http://localhost/api/health', 'GET');

        $response = (new ForceCanonicalHttpsScheme)->handle(
            $request,
            fn (Request $handledRequest): JsonResponse => response()->json([
                'root' => $handledRequest->root(),
                'secure' => $handledRequest->secure(),
            ]),
        );

        $this->assertSame('http://localhost', $response->getData(true)['root']);
        $this->assertFalse($response->getData(true)['secure']);
    }
}
