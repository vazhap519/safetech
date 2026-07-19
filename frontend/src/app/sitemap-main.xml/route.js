import {
  buildSitemapApiUrl,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const revalidate = 300;

export async function GET() {
  const response = await safeFetchJson(buildSitemapApiUrl("/seo"));
  const seoPages = Array.isArray(response?.data) ? response.data : [];
  const seoByKey = new Map(seoPages.map((page) => [page.key, page]));
  const pages = [
    { key: "home", path: "/", changefreq: "daily", priority: "1.0" },
    { key: "about", path: "/about", changefreq: "monthly", priority: "0.6" },
    { key: "services", path: "/services", changefreq: "weekly", priority: "0.9" },
    { key: "service-calculator", path: "/service-calculator", changefreq: "weekly", priority: "0.8" },
    { key: "blog", path: "/blog", changefreq: "weekly", priority: "0.7" },
    { key: "projects", path: "/projects", changefreq: "weekly", priority: "0.7" },
    { key: "contact", path: "/contact", changefreq: "monthly", priority: "0.5" },
    { key: "privacy", path: "/privacy", changefreq: "yearly", priority: "0.2" },
  ];

  return xmlResponse(
    urlset(
      pages
        .filter((page) => seoByKey.get(page.key)?.noindex !== true)
        .flatMap((page) => {
          const seoPage = seoByKey.get(page.key);

          return localizedUrlEntries(page.path, {
            ...(seoPage?.updated_at ? { lastmod: seoPage.updated_at } : {}),
            changefreq: page.changefreq,
            priority: page.priority,
          });
        }),
    ),
  );
}
