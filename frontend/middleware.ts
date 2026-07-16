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

function setRequestLocale(headers: Headers, locale: string) {
    headers.set("x-safetech-locale", locale);

    const cookieHeader = headers.get("cookie") || "";
    const nextCookie = `${LOCALE_COOKIE_NAME}=${locale}`;

    if (!cookieHeader) {
        headers.set("cookie", nextCookie);
        return;
    }

    const cookies = cookieHeader
        .split(";")
        .map((cookie) => cookie.trim())
        .filter((cookie) => cookie && !cookie.startsWith(`${LOCALE_COOKIE_NAME}=`));

    cookies.push(nextCookie);
    headers.set("cookie", cookies.join("; "));
}

export function middleware(request: NextRequest) {
    const { pathname } = request.nextUrl;
    const firstSegment = pathname.split("/").filter(Boolean)[0];

    if (firstSegment === DEFAULT_LOCALE) {
        const redirectUrl = request.nextUrl.clone();
        redirectUrl.pathname = buildInternalPath(pathname);

        return NextResponse.redirect(redirectUrl, 308);
    }

    const requestHeaders = new Headers(request.headers);

    if (!isNonDefaultLocale(firstSegment)) {
        setRequestLocale(requestHeaders, DEFAULT_LOCALE);

        return NextResponse.next({
            request: {
                headers: requestHeaders,
            },
        });
    }

    const locale = normalizeLocale(firstSegment);
    setRequestLocale(requestHeaders, locale);

    const response = NextResponse.next({
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
