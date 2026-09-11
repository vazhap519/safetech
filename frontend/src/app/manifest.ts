import type { MetadataRoute } from "next";

import { SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function manifest(): Promise<MetadataRoute.Manifest> {
    const { branding } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const customFavicon = branding.favicon?.trim();
    const icons: NonNullable<MetadataRoute.Manifest["icons"]> = [
        {
            src: "/icon-192.png",
            sizes: "192x192",
            type: "image/png",
            purpose: "any",
        },
        {
            src: "/icon-512.png",
            sizes: "512x512",
            type: "image/png",
            purpose: "any",
        },
    ];

    if (
        customFavicon &&
        !icons.some((icon) => icon.src === customFavicon)
    ) {
        icons.unshift({ src: customFavicon, purpose: "any" });
    }

    return {
        id: "/",
        name: siteName,
        short_name: siteName,
        description: branding.tagline || siteName,
        start_url: "/",
        scope: "/",
        display: "standalone",
        lang: "ka-GE",
        categories: ["business", "productivity", "utilities"],
        prefer_related_applications: false,
        background_color: "#070B14",
        theme_color: "#070B14",
        icons,
    };
}
