import { NextResponse, type NextRequest } from "next/server";
import { SERVICE_CANONICAL_ALIASES, CANONICAL_SERVICE_SLUGS } from "./src/lib/service-canonical-aliases";

const LOCALE_COOKIE_NAME = "safetech_locale";



import {
    DEFAULT_LOCALE,
    getLanguageTag,
    isSupportedLocale,
    normalizeLocale,
    stripLocalePrefix,
    type Locale,
} from "@/lib/locales";

function localeFromRequest(request: NextRequest): Locale {
    const firstSegment = request.nextUrl.pathname.split("/").filter(Boolean)[0];

    if (isSupportedLocale(firstSegment)) {
        return normalizeLocale(firstSegment);
    }

    return DEFAULT_LOCALE;
}

function withLocaleHeaders(response: NextResponse, locale: Locale) {
    response.headers.set("Content-Language", getLanguageTag(locale));

    return response;
}

function withLocaleCookie(response: NextResponse, locale: Locale) {
    response.cookies.set(LOCALE_COOKIE_NAME, locale, {
        path: "/",
        sameSite: "lax",
    });

    return withLocaleHeaders(response, locale);
}

export function proxy(request: NextRequest) {
    const segments = request.nextUrl.pathname.split("/").filter(Boolean);
    const firstSegment = segments[0];
    const locale = localeFromRequest(request);
    const hasLocalePrefix = isSupportedLocale(firstSegment);
    const serviceRootIndex = hasLocalePrefix ? 1 : 0;
    const serviceSlugIndex = serviceRootIndex + 1;
    const serviceSlug = segments[serviceSlugIndex];
    const canonicalServiceSlug =
        segments[serviceRootIndex] === "services" && serviceSlug
            ? SERVICE_CANONICAL_ALIASES[serviceSlug]
            : undefined;
    const legacyQueryService = request.nextUrl.searchParams.get("service")?.trim();
    const isServicesIndex =
        segments[serviceRootIndex] === "services" &&
        segments.length === serviceRootIndex + 1;

    if (
        isServicesIndex &&
        legacyQueryService &&
        /^[a-z0-9-]+$/.test(legacyQueryService)
    ) {
        const url = request.nextUrl.clone();
        const targetService =
            SERVICE_CANONICAL_ALIASES[legacyQueryService] || (CANONICAL_SERVICE_SLUGS.has(legacyQueryService) ? legacyQueryService : null);
        const localePrefix =
            hasLocalePrefix && firstSegment !== DEFAULT_LOCALE
                ? `/${firstSegment}`
                : "";

        url.pathname = targetService ? `${localePrefix}/services/${targetService}` : `${localePrefix}/services`;
        url.search = "";

        return withLocaleCookie(NextResponse.redirect(url, 308), locale);
    }

    // A legacy query must not produce a second indexable catalog URL, even if
    // the service value is empty or invalid. Preserve the clean catalog path.
    if (isServicesIndex && request.nextUrl.searchParams.has("service")) {
        const url = request.nextUrl.clone();
        url.searchParams.delete("service");
        return withLocaleCookie(NextResponse.redirect(url, 308), locale);
    }

    if (firstSegment === DEFAULT_LOCALE || canonicalServiceSlug) {
        const url = request.nextUrl.clone();
        const redirectedSegments = [...segments];

        if (firstSegment === DEFAULT_LOCALE) {
            redirectedSegments.shift();
        }

        const redirectedServiceRootIndex =
            isSupportedLocale(redirectedSegments[0]) ? 1 : 0;
        const redirectedServiceSlugIndex = redirectedServiceRootIndex + 1;
        const redirectedServiceSlug = redirectedSegments[redirectedServiceSlugIndex];
        const redirectedCanonicalSlug =
            redirectedSegments[redirectedServiceRootIndex] === "services" &&
            redirectedServiceSlug
                ? SERVICE_CANONICAL_ALIASES[redirectedServiceSlug]
                : undefined;

        if (redirectedCanonicalSlug) {
            redirectedSegments[redirectedServiceSlugIndex] = redirectedCanonicalSlug;
        }

        url.pathname = "/" + redirectedSegments.join("/");

        return withLocaleCookie(NextResponse.redirect(url, 308), locale);
    }

    const requestHeaders = new Headers(request.headers);
    requestHeaders.set("x-safetech-locale", locale);

    const response = NextResponse.next({
        request: {
            headers: requestHeaders,
        },
    });

    return withLocaleCookie(response, locale);
}

export const config = {
    matcher: [
        "/((?!api|_next/static|_next/image|favicon.ico|icon-192.png|icon-512.png|manifest.webmanifest|robots.txt|sitemap.*\\.xml|.*\\..*).*)",
    ],
};
