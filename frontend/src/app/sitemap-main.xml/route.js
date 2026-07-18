import { localizedUrlEntries, urlset, xmlResponse } from "@/lib/sitemap";

export const revalidate = 300;

export async function GET() {
  const now = new Date().toISOString();

  return xmlResponse(
    urlset([
      ...localizedUrlEntries("/", { lastmod: now, changefreq: "daily", priority: "1.0" }),
      ...localizedUrlEntries("/about", { lastmod: now, changefreq: "monthly", priority: "0.6" }),
      ...localizedUrlEntries("/services", { lastmod: now, changefreq: "weekly", priority: "0.9" }),
      ...localizedUrlEntries("/blog", { lastmod: now, changefreq: "weekly", priority: "0.7" }),
      ...localizedUrlEntries("/projects", { lastmod: now, changefreq: "weekly", priority: "0.7" }),
      ...localizedUrlEntries("/contact", { lastmod: now, changefreq: "monthly", priority: "0.5" }),
      ...localizedUrlEntries("/privacy", { lastmod: now, changefreq: "yearly", priority: "0.2" }),
    ]),
  );
}
