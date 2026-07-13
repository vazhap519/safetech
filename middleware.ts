import { NextResponse, type NextRequest } from "next/server";

import {
    DEFAULT_LOCALE,
    LOCALE_COOKIE_NAME,
    isNonDefaultLocale,
    normalizeLocale,
} from "./src/lib/locales";

function buildInternalPath(pathname: string) {
    const segments = pathname.split("/").filter(Boolean);
    const localizedPath = `/${segments.slice(1).join("/")}`;

    return localizedPath === "/" ? "/" : localizedPath;
}

export function middleware(request: NextRequest) {
    const { pathname } = request.nextUrl;
    const firstSegment = pathname.split("/").filter(Boolean)[0];

    if (firstSegment === DEFAULT_LOCALE) {
        const redirectUrl = request.nextUrl.clone();
        redirectUrl.pathname = buildInternalPath(pathname);

        return NextResponse.redirect(redirectUrl, 308);
    }

    if (!isNonDefaultLocale(firstSegment)) {
        return NextResponse.next();
    }

    const locale = normalizeLocale(firstSegment);
    const rewrittenUrl = request.nextUrl.clone();
    rewrittenUrl.pathname = buildInternalPath(pathname);

    const requestHeaders = new Headers(request.headers);
    requestHeaders.set("x-safetech-locale", locale);

    const response = NextResponse.rewrite(rewrittenUrl, {
        request: {
            headers: requestHeaders,
        },
    });

    response.cookies.set(LOCALE_COOKIE_NAME, locale, {
        httpOnly: false,
        maxAge: 60 * 60 * 24 * 365,
        path: "/",
        sameSite: "lax",
    });

    return response;
}

export const config = {
    matcher: ["/((?!api|_next|favicon.ico|robots.txt|sitemap.xml|manifest.webmanifest|.*\\..*).*)"],
};
