import type { MetadataRoute } from "next";

import { getBackendProjectSlugs, getBackendServiceSlugs } from "@/lib/backend";
import { absoluteSiteUrl, buildLanguageAlternates } from "@/lib/seo";

function uniqueSlugs(slugs: string[]) {
    return [...new Set(slugs.filter(Boolean))];
}

function buildEntry(
    path: string,
    lastModified: Date,
    changeFrequency: NonNullable<
        MetadataRoute.Sitemap[number]["changeFrequency"]
    >,
    priority: number,
): MetadataRoute.Sitemap[number] {
    return {
        url: absoluteSiteUrl(path),
        lastModified,
        changeFrequency,
        priority,
        alternates: {
            languages: buildLanguageAlternates(path),
        },
    };
}

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
    const [backendServiceSlugs, backendProjectSlugs] = await Promise.all([
        getBackendServiceSlugs(),
        getBackendProjectSlugs(),
    ]);
    const now = new Date();

    const staticPages: MetadataRoute.Sitemap = [
        buildEntry("/", now, "weekly", 1),
        buildEntry("/about", now, "monthly", 0.8),
        buildEntry("/services", now, "weekly", 0.9),
        buildEntry("/projects", now, "weekly", 0.8),
        buildEntry("/contact", now, "monthly", 0.7),
    ];

    const servicePages: MetadataRoute.Sitemap = uniqueSlugs(
        backendServiceSlugs,
    ).map((slug) => buildEntry(`/services/${slug}`, now, "monthly", 0.8));

    const projectPages: MetadataRoute.Sitemap = uniqueSlugs(
        backendProjectSlugs,
    ).map((slug) => buildEntry(`/projects/${slug}`, now, "monthly", 0.8));

    return [...staticPages, ...servicePages, ...projectPages];
}
