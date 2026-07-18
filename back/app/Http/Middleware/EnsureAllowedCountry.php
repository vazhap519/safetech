<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAllowedCountry
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('geo.enabled') || $request->is('api/health') || $request->isMethod('OPTIONS')) {
            return $next($request);
        }

        $country = $this->countryFromRequest($request);
        $allowedCountries = config('geo.allowed_countries', ['GE']);

        if ($country !== null && in_array($country, $allowedCountries, true)) {
            return $next($request);
        }

        if ($country === null && ! config('geo.block_unknown', true)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'This content is available in Georgia only.',
        ], 403)->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function countryFromRequest(Request $request): ?string
    {
        foreach (config('geo.country_headers', []) as $header) {
            $country = strtoupper(trim((string) $request->headers->get($header, '')));

            if (preg_match('/^[A-Z]{2}$/', $country)) {
                return $country;
            }
        }

        return null;
    }
}
