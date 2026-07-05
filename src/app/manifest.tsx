import type { MetadataRoute } from "next";

import { getSiteSettings } from "@/lib/site-settings";

export default async function manifest(): Promise<MetadataRoute.Manifest> {
    const { branding } = await getSiteSettings();

    return {
        name: branding.siteName,
        short_name: branding.siteName,
        description: `${branding.siteName}: CCTV, დაშვების კონტროლი, ქსელური და სერვერული ინფრასტრუქტურა ბიზნესისთვის.`,
        start_url: "/",
        display: "standalone",
        background_color: "#070B14",
        theme_color: "#070B14",
        icons: [
            {
                src: branding.favicon,
                purpose: "any",
            },
        ],
    };
}
