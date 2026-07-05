import type { MetadataRoute } from "next";

import { serviceDetails } from "@/features/service-detail/data/services";
import { getBackendProjectSlugs, getBackendServiceSlugs } from "@/lib/backend";
import { projectDetails } from "@/lib/projectDetails";
import { absoluteSiteUrl } from "@/lib/seo";

function uniqueSlugs(slugs: string[]) {
    return [...new Set(slugs.filter(Boolean))];
}

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
    const [backendServiceSlugs, backendProjectSlugs] = await Promise.all([
        getBackendServiceSlugs(),
        getBackendProjectSlugs(),
    ]);
    const now = new Date();

    const staticPages: MetadataRoute.Sitemap = [
        {
            url: absoluteSiteUrl("/"),
            lastModified: now,
            changeFrequency: "weekly",
            priority: 1,
        },
        {
            url: absoluteSiteUrl("/about"),
            lastModified: now,
            changeFrequency: "monthly",
            priority: 0.8,
        },
        {
            url: absoluteSiteUrl("/services"),
            lastModified: now,
            changeFrequency: "weekly",
            priority: 0.9,
        },
        {
            url: absoluteSiteUrl("/projects"),
            lastModified: now,
            changeFrequency: "weekly",
            priority: 0.8,
        },
        {
            url: absoluteSiteUrl("/contact"),
            lastModified: now,
            changeFrequency: "monthly",
            priority: 0.7,
        },
    ];

    const servicePages: MetadataRoute.Sitemap = uniqueSlugs([
        ...serviceDetails.map(({ slug }) => slug),
        ...backendServiceSlugs,
    ]).map((slug) => ({
        url: absoluteSiteUrl(`/services/${slug}`),
        lastModified: now,
        changeFrequency: "monthly" as const,
        priority: 0.8,
    }));

    const projectPages: MetadataRoute.Sitemap = uniqueSlugs([
        ...projectDetails.map(({ slug }) => slug),
        ...backendProjectSlugs,
    ]).map((slug) => ({
        url: absoluteSiteUrl(`/projects/${slug}`),
        lastModified: now,
        changeFrequency: "monthly" as const,
        priority: 0.8,
    }));

    return [...staticPages, ...servicePages, ...projectPages];
}
