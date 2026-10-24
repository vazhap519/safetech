import { NextResponse, type NextRequest } from "next/server";

const LOCALE_COOKIE_NAME = "safetech_locale";

const SERVICE_CANONICAL_ALIASES: Record<string, string> = {
    "ip-camera-installation": "security-camera-installation",
    "video-surveillance-system-installation": "security-camera-installation",
    "intercom-installation": "intercom-access-control-installation",
    "access-control-system-installation": "intercom-access-control-installation",
    "access-control-installation": "intercom-access-control-installation",
    "barrier-gate-setup": "barrier-gate-installation",
    "it-technical-support": "business-it-support",
    "rack-assembly-cable-management": "low-voltage-cabling",
    "network-rack-installation": "low-voltage-cabling",
    "network-cable-installation": "low-voltage-cabling",
    "patch-panel-network-outlet-installation": "low-voltage-cabling",
    "patch-panel-installation": "low-voltage-cabling",
    "cat6-cabling": "low-voltage-cabling",
    "structured-cabling": "low-voltage-cabling",
    "structured-cabling-installation": "low-voltage-cabling",
    "telecommunications-infrastructure-installation": "low-voltage-cabling",
    "communications-infrastructure": "low-voltage-cabling",
    "router-wifi-configuration": "network-installation-configuration",
    "lan-installation": "network-installation-configuration",
    "lan-network-installation": "network-installation-configuration",
    "wifi-network-installation": "network-installation-configuration",
    "network-diagnostics": "network-installation-configuration",
    "computer-network-diagnostics": "network-installation-configuration",
    "personal-computer-assembly": "operating-system-installation",
    "custom-computer-build": "operating-system-installation",
    "computer-upgrade-optimization": "operating-system-installation",
    "computer-component-replacement": "operating-system-installation",
    "computer-preventive-maintenance": "operating-system-installation",
    "computer-peripheral-troubleshooting": "operating-system-installation",
    "computer-setup-optimization": "operating-system-installation",
    "computer-component-upgrades": "operating-system-installation",
    "computer-cleaning-maintenance": "operating-system-installation",
    "computer-peripheral-setup": "operating-system-installation",
    "macos-installation-configuration": "operating-system-installation",
    "macos-installation": "operating-system-installation",
    "macbook-imac-software-setup": "operating-system-installation",
    "mac-software-setup": "operating-system-installation",
    "mac-software-installation": "operating-system-installation",
    "mac-app-installation": "operating-system-installation",
    "mac-diagnostics-repair": "operating-system-installation",
    "mac-diagnostics": "operating-system-installation",
    "mac-data-recovery-backup": "operating-system-installation",
    "mac-backup-migration": "operating-system-installation",
    "pos-system-installation": "business-it-support",
    "computers-workstations-setup": "business-it-support",
    "workstation-setup": "business-it-support",
    "microsoft-365-setup-migration": "business-it-support",
    "microsoft-365-migration": "business-it-support",
    "data-backup-recovery": "business-it-support",
    "backup-setup": "business-it-support",

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
