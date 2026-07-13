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
    return title.includes(siteName) ? title : `${title} | ${siteName}`;
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
    const fullTitle = withSiteTitle(title, siteName);
    const socialImage = absoluteSiteUrl(image);
    const languageAlternates = buildLanguageAlternates(path);
    const alternateLocales = supportedLocales
        .filter((item) => item !== locale)
        .map(getOgLocale);

    return {
        title: {
            absolute: fullTitle,
        },
        description,
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
            description,
            url,
            siteName,
            locale: getOgLocale(locale),
            alternateLocale: alternateLocales,
            type,
            images: [
                {
                    url: socialImage,
                    alt: title,
                },
            ],
        },
        twitter: {
            card: "summary_large_image",
            title: fullTitle,
            description,
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
