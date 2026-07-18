import type { Metadata } from "next";
import { getOgLocale, supportedLocales, type Locale } from "@/lib/locales";
import { DEFAULT_LOCALE, localizePath } from "@/lib/locales";

type SeoProps = {
    title: string;
    description: string;
    path: string;
    locale?: Locale;
    keywords?: string[];
    image?: string;
    siteName?: string;
    type?: "website" | "article";
};

type LegacySeoProps = {
    seo?: Record<string, unknown>;
    title?: string;
    description?: string;
    image?: string;
    path?: string;
    type?: "website" | "article";
    keywords?: unknown[];
    canonical?: string;
    noindex?: boolean;
    og?: Record<string, unknown>;
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
            locale,
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
            index: true,
            follow: true,
            googleBot: {
                index: true,
                follow: true,
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

function normalizeKeywords(keywords: unknown): string[] {
    if (!Array.isArray(keywords)) return [];

    return keywords
        .map((item) => {
            if (typeof item === "string") return item;
            if (item && typeof item === "object") {
                const record = item as Record<string, unknown>;
                return cleanText(record.value || record.keyword);
            }

            return "";
        })
        .map(cleanText)
        .filter(Boolean);
}

function normalizeLegacySeo(seo: unknown): Record<string, unknown> {
    if (!seo || typeof seo !== "object" || Array.isArray(seo)) return {};

    const record = seo as Record<string, unknown>;
    const data = record.data;

    if (data && typeof data === "object" && !Array.isArray(data)) {
        const dataRecord = data as Record<string, unknown>;
        const meta = dataRecord.meta;

        if (meta && typeof meta === "object" && !Array.isArray(meta)) {
            return meta as Record<string, unknown>;
        }

        return dataRecord;
    }

    const meta = record.meta;

    return meta && typeof meta === "object" && !Array.isArray(meta)
        ? (meta as Record<string, unknown>)
        : record;
}

function resolveCanonicalUrl(value: unknown, path: string): string {
    return absoluteSiteUrl(cleanText(value) || path || "/");
}

export function buildMetadata({
    seo,
    title,
    description,
    image,
    path = "/",
    type = "website",
    keywords = [],
    canonical,
    noindex,
    og = {},
}: LegacySeoProps = {}): Metadata {
    const normalized = normalizeLegacySeo(seo);
    const ogRecord =
        normalized.og && typeof normalized.og === "object"
            ? (normalized.og as Record<string, unknown>)
            : {};
    const mergedOg = { ...ogRecord, ...og };
    const resolvedTitle =
        cleanText(title) || cleanText(normalized.title) || SITE_NAME;
    const resolvedDescription =
        cleanText(description) ||
        cleanText(normalized.description) ||
        "Professional IT infrastructure and security services.";
    const resolvedKeywords = normalizeKeywords(keywords).length
        ? normalizeKeywords(keywords)
        : normalizeKeywords(normalized.keywords);
    const resolvedCanonical = resolveCanonicalUrl(
        canonical || normalized.canonical,
        path,
    );
    const resolvedImage = absoluteSiteUrl(
        cleanText(image) ||
            cleanText(mergedOg.image) ||
            cleanText(normalized.image) ||
            DEFAULT_SOCIAL_IMAGE,
    );
    const shouldNoindex =
        typeof noindex === "boolean"
            ? noindex
            : Boolean(normalized.noindex);
    const fullTitle = withSiteTitle(resolvedTitle);

    return {
        metadataBase: new URL(SITE_URL),
        title: fullTitle,
        description: resolvedDescription,
        ...(resolvedKeywords.length ? { keywords: resolvedKeywords } : {}),
        alternates: {
            canonical: resolvedCanonical,
        },
        robots: {
            index: !shouldNoindex,
            follow: !shouldNoindex,
            googleBot: {
                index: !shouldNoindex,
                follow: !shouldNoindex,
                "max-image-preview": "large",
                "max-snippet": -1,
                "max-video-preview": -1,
            },
        },
        openGraph: {
            title: cleanText(mergedOg.title) || fullTitle,
            description:
                cleanText(mergedOg.description) || resolvedDescription,
            url: resolvedCanonical,
            siteName: SITE_NAME,
            type,
            images: [
                {
                    url: resolvedImage,
                    width: 1200,
                    height: 630,
                    alt: resolvedTitle,
                },
            ],
        },
        twitter: {
            card: "summary_large_image",
            title: fullTitle,
            description: resolvedDescription,
            images: [resolvedImage],
        },
    };
}
