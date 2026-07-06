import type { MetadataRoute } from "next";

import { SITE_URL, absoluteSiteUrl } from "@/lib/seo";

export default function robots(): MetadataRoute.Robots {
    return {
        rules: [
            {
                userAgent: "*",
                allow: "/",
                disallow: ["/admin", "/api"],
            },
        ],
        sitemap: absoluteSiteUrl("/sitemap.xml"),
        host: new URL(SITE_URL).host,
    };
}
