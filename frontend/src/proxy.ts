import { NextResponse, type NextRequest } from "next/server";

const LOCALE_COOKIE_NAME = "safetech_locale";

const SERVICE_CANONICAL_ALIASES: Record<string, string> = {
    "ip-camera-installation": "security-camera-installation",
    "video-surveillance-system-installation": "security-camera-installation",
    "intercom-installation": "intercom-access-control-installation",
    "access-control-system-installation": "access-control-installation",
    "barrier-gate-setup": "barrier-gate-installation",
    "personal-computer-assembly": "custom-computer-build",
    "computer-upgrade-optimization": "computer-setup-optimization",
    "computer-component-replacement": "computer-component-upgrades",
    "computer-preventive-maintenance": "computer-cleaning-maintenance",
    "computer-peripheral-troubleshooting": "computer-peripheral-setup",
    "cat6-cabling": "network-cable-installation",
    "lan-installation": "lan-network-installation",
    "wifi-network-installation": "router-wifi-configuration",
    "network-rack-installation": "rack-assembly-cable-management",
    "patch-panel-installation": "patch-panel-network-outlet-installation",
    "it-technical-support": "business-it-support",
    "computers-workstations-setup": "workstation-setup",
    "microsoft-365-setup-migration": "microsoft-365-migration",
    "computer-network-diagnostics": "network-diagnostics",
    "data-backup-recovery": "backup-setup",
    "macos-installation-configuration": "macos-installation",
    "macbook-imac-software-setup": "mac-software-setup",
    "mac-software-installation": "mac-app-installation",
    "mac-diagnostics-repair": "mac-diagnostics",
    "mac-data-recovery-backup": "mac-backup-migration",
    "structured-cabling-installation": "structured-cabling",
    "telecommunications-infrastructure-installation": "communications-infrastructure",
};

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
            SERVICE_CANONICAL_ALIASES[legacyQueryService] || legacyQueryService;
        const localePrefix =
            hasLocalePrefix && firstSegment !== DEFAULT_LOCALE
                ? `/${firstSegment}`
                : "";

        url.pathname = `${localePrefix}/services/${targetService}`;
        url.search = "";

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
        "/((?!api|_next/static|_next/image|favicon.ico|icon-192.png|icon-512.png|manifest.webmanifest|robots.txt|sitemap.*\\.xml|[a-fA-F0-9]{32,128}|.*\\..*).*)",
    ],
};
