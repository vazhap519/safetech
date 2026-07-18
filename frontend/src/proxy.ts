import { NextResponse, type NextRequest } from "next/server";

import {
    DEFAULT_LOCALE,
    LOCALE_COOKIE_NAME,
    isSupportedLocale,
    normalizeLocale,
    stripLocalePrefix,
    type Locale,
} from "@/lib/locales";

const COUNTRY_HEADERS = [
    "cf-ipcountry",
    "x-country-code",
    "x-vercel-ip-country",
    "cloudfront-viewer-country",
    "fastly-client-geo-country-code",
    "x-appengine-country",
    "x-geo-country",
    "x-forwarded-country",
];

function parseBoolean(value: string | undefined, fallback = false) {
    if (value === undefined) return fallback;

    return ["1", "true", "yes", "on"].includes(value.toLowerCase());
}

function geoRestrictionEnabled() {
    return parseBoolean(process.env.GEO_BLOCK_ENABLED);
}

function blockUnknownCountry() {
    return parseBoolean(process.env.GEO_BLOCK_UNKNOWN_COUNTRY, false);
}

function allowedCountries() {
    return (process.env.GEO_ALLOWED_COUNTRIES || "GE")
        .split(",")
        .map((country) => country.trim().toUpperCase())
        .filter((country) => /^[A-Z]{2}$/.test(country));
}

function countryFromRequest(request: NextRequest) {
    for (const header of COUNTRY_HEADERS) {
        const country = request.headers.get(header)?.trim().toUpperCase();

        if (country && /^[A-Z]{2}$/.test(country)) {
            return country;
        }
    }

    return null;
}

function isAllowedCountry(request: NextRequest) {
    if (!geoRestrictionEnabled()) return true;

    const country = countryFromRequest(request);

    if (country) {
        return allowedCountries().includes(country);
    }

    return !blockUnknownCountry();
}

function blockedCountryResponse() {
    return new NextResponse(
        `<!doctype html><html lang="ka"><head><meta charset="utf-8"><meta name="robots" content="noindex,nofollow"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Access restricted</title></head><body style="margin:0;min-height:100vh;display:grid;place-items:center;background:#080d18;color:#f8fafc;font-family:Arial,sans-serif;text-align:center;padding:24px"><main><h1 style="font-size:28px;margin:0 0 12px">საიტი ხელმისაწვდომია მხოლოდ საქართველოდან</h1><p style="margin:0;color:#cbd5e1">This website is available in Georgia only.</p></main></body></html>`,
        {
            status: 403,
            headers: {
                "Content-Type": "text/html; charset=utf-8",
                "X-Robots-Tag": "noindex, nofollow",
                "Cache-Control": "private, no-store",
            },
        },
    );
}

function localeFromRequest(request: NextRequest): Locale {
    const firstSegment = request.nextUrl.pathname.split("/").filter(Boolean)[0];

    if (isSupportedLocale(firstSegment)) {
        return normalizeLocale(firstSegment);
    }

    return normalizeLocale(request.cookies.get(LOCALE_COOKIE_NAME)?.value);
}

function persistLocale(response: NextResponse, locale: Locale) {
    response.cookies.set(LOCALE_COOKIE_NAME, locale, {
        httpOnly: false,
        maxAge: 60 * 60 * 24 * 365,
        path: "/",
        sameSite: "lax",
    });
}

export function proxy(request: NextRequest) {
    const firstSegment = request.nextUrl.pathname.split("/").filter(Boolean)[0];
    const locale = localeFromRequest(request);

    if (!isAllowedCountry(request)) {
        return blockedCountryResponse();
    }

    if (firstSegment === DEFAULT_LOCALE) {
        const url = request.nextUrl.clone();
        url.pathname = stripLocalePrefix(url.pathname);

        const response = NextResponse.redirect(url, 308);
        persistLocale(response, DEFAULT_LOCALE);

        return response;
    }

    const requestHeaders = new Headers(request.headers);
    requestHeaders.set("x-safetech-locale", locale);

    const response = NextResponse.next({
        request: {
            headers: requestHeaders,
        },
    });

    persistLocale(response, locale);

    return response;
}

export const config = {
    matcher: [
        "/((?!api|_next/static|_next/image|favicon.ico|icon-192.png|icon-512.png|manifest.json|manifest.webmanifest|robots.txt|sitemap.*\\.xml|.*\\..*).*)",
    ],
};
