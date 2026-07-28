import type { MetadataRoute } from "next";

import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function robots(): Promise<MetadataRoute.Robots> {
    const { seo } = await getSiteSettings();

    return {
        rules: [
            seo.robotsIndex
                ? {
                      userAgent: "*",
                      allow: "/",
                      disallow: ["/admin", "/api"],
                  }
                : {
                      userAgent: "*",
                      disallow: "/",
                  },
        ],
        sitemap: absoluteSiteUrl("/sitemap.xml"),
    };
}
