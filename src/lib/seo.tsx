import type { Metadata } from "next";

type SeoProps = {
    title: string;
    description: string;
    path: string;
    keywords?: string[];
    image?: string;
    siteName?: string;
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

export function withSiteTitle(title: string, siteName = SITE_NAME): string {
    return title.includes(siteName) ? title : `${title} | ${siteName}`;
}

export function createMetadata({
    title,
    description,
    path,
    keywords = [],
    image = DEFAULT_SOCIAL_IMAGE,
    siteName = SITE_NAME,
}: SeoProps): Metadata {
    const url = absoluteSiteUrl(path);
    const fullTitle = withSiteTitle(title, siteName);
    const socialImage = absoluteSiteUrl(image);

    return {
        title: {
            absolute: fullTitle,
        },
        description,
        keywords,
        alternates: {
            canonical: url,
        },
        openGraph: {
            title: fullTitle,
            description,
            url,
            siteName,
            locale: "ka_GE",
            type: "website",
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
        },
    };
}
