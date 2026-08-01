import { NextResponse, type NextRequest } from "next/server";

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

export function proxy(request: NextRequest) {
    const firstSegment = request.nextUrl.pathname.split("/").filter(Boolean)[0];
    const locale = localeFromRequest(request);

    if (firstSegment === DEFAULT_LOCALE) {
        const url = request.nextUrl.clone();
        url.pathname = stripLocalePrefix(url.pathname);

        return withLocaleHeaders(NextResponse.redirect(url, 308), locale);
    }

    const requestHeaders = new Headers(request.headers);
    requestHeaders.set("x-safetech-locale", locale);

    return withLocaleHeaders(
        NextResponse.next({
            request: {
                headers: requestHeaders,
            },
        }),
        locale,
    );
}

export const config = {
    matcher: [
        "/((?!api|_next/static|_next/image|favicon.ico|icon-192.png|icon-512.png|manifest.webmanifest|robots.txt|sitemap.*\\.xml|.*\\..*).*)",
    ],
};
