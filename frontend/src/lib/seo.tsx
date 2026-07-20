import type { Metadata } from "next";
import {
    getLanguageTag,
    getOgLocale,
    supportedLocales,
    type Locale,
} from "@/lib/locales";
import { DEFAULT_LOCALE, localizePath } from "@/lib/locales";

export type SeoProps = {
    title: string;
    description: string;
    path: string;
    locale?: Locale;
    keywords?: string[];
    image?: string;
    siteName?: string;
    type?: "website" | "article";
    noindex?: boolean;
};

export const SITE_NAME = "SafeTech";
export const DEFAULT_SOCIAL_IMAGE = "/brand-preview.svg";

function resolveSiteUrl() {
    const configured = process.env.NEXT_PUBLIC_SITE_URL?.trim();
    const fallback = "https://safetech.ge";

    try {
        return new URL(configured || fallback).toString();
    } catch {
        return fallback;
    }
}

export const SITE_URL = resolveSiteUrl();

export function absoluteSiteUrl(pathOrUrl: string): string {
    return new URL(pathOrUrl, SITE_URL).toString();
}

export function absoluteLocalizedUrl(
    path: string,
    locale: Locale = DEFAULT_LOCALE,
): string {
    return absoluteSiteUrl(localizePath(path, locale));
}

export function buildLanguageAlternates(path: string) {
    return Object.fromEntries(
        supportedLocales.map((locale) => [
            getLanguageTag(locale),
            absoluteLocalizedUrl(path, locale),
        ]),
    );
}

export function localizeHref<T extends string | { pathname?: string | null }>(
    href: T,
    locale: Locale = DEFAULT_LOCALE,
): T {
    if (typeof href === "string") {
        if (
            !href.startsWith("/") ||
            href.startsWith("//") ||
            href.startsWith("/api") ||
            href.startsWith("/admin")
        ) {
            return href;
        }

        return localizePath(href, locale) as T;
    }

    if (href && typeof href === "object" && typeof href.pathname === "string") {
        return {
            ...href,
            pathname: localizePath(href.pathname, locale),
        } as T;
    }

    return href;
}

export function withSiteTitle(title: string, siteName = SITE_NAME): string {
    const cleanTitle = cleanText(title);
    const cleanSiteName = cleanText(siteName) || SITE_NAME;

    if (!cleanTitle) return cleanSiteName;

    return cleanTitle.includes(cleanSiteName)
        ? cleanTitle
        : `${cleanTitle} | ${cleanSiteName}`;
}

export function createMetadata({
    title,
    description,
    path,
    locale = DEFAULT_LOCALE,
    keywords = [],
    image = DEFAULT_SOCIAL_IMAGE,
    siteName = SITE_NAME,
    type = "website",
    noindex = false,
}: SeoProps): Metadata {
    const url = absoluteLocalizedUrl(path, locale);
    const resolvedSiteName = cleanText(siteName) || SITE_NAME;
    const resolvedTitle = cleanText(title) || resolvedSiteName;
    const resolvedDescription = cleanText(description) || resolvedSiteName;
    const fullTitle = withSiteTitle(resolvedTitle, resolvedSiteName);
    const socialImage = absoluteSiteUrl(image);
    const languageAlternates = buildLanguageAlternates(path);
    const alternateLocales = supportedLocales
        .filter((item) => item !== locale)
        .map(getOgLocale);

    return {
        title: {
            absolute: fullTitle,
        },
        description: resolvedDescription,
        keywords,
        alternates: {
            canonical: url,
            languages: {
                ...languageAlternates,
                "x-default": absoluteSiteUrl(path),
            },
        },
        openGraph: {
            title: fullTitle,
            description: resolvedDescription,
            url,
            siteName: resolvedSiteName,
            locale: getOgLocale(locale),
            alternateLocale: alternateLocales,
            type,
            images: [
                {
                    url: socialImage,
                    alt: resolvedTitle,
                },
            ],
        },
        twitter: {
            card: "summary_large_image",
            title: fullTitle,
            description: resolvedDescription,
            images: [socialImage],
        },
        robots: {
            index: !noindex,
            follow: !noindex,
            googleBot: {
                index: !noindex,
                follow: !noindex,
                "max-image-preview": "large",
                "max-snippet": -1,
                "max-video-preview": -1,
            },
        },
    };
}

function cleanText(value: unknown): string {
    return typeof value === "string"
        ? value.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim()
        : "";
}
