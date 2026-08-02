<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalHttpsScheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonicalScheme = parse_url((string) config('app.url'), PHP_URL_SCHEME);

        if ($canonicalScheme === 'https') {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
            $request->headers->set('X-Forwarded-Proto', 'https');
            $request->headers->set('X-Forwarded-Port', '443');
        }

        return $next($request);
    }
}
