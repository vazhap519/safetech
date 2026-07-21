import {
  buildSitemapApiUrl,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const [response, privacyResponse] = await Promise.all([
    safeFetchJson(buildSitemapApiUrl("/seo")),
    safeFetchJson(buildSitemapApiUrl("/privacy", { locale: "ka" })),
  ]);

  if (!response || !privacyResponse) {
    throw new Error("Unable to load required CMS data for the main sitemap");
  }

  const seoPages = Array.isArray(response?.data) ? response.data : [];
  const seoByKey = new Map(seoPages.map((page) => [page.key, page]));
  const privacyContent = privacyResponse?.data ?? privacyResponse;
  const hasPublishedPrivacyPolicy = [
    privacyContent?.title,
    privacyContent?.highlight,
    privacyContent?.content,
  ].some((value) => typeof value === "string" && value.trim().length > 0);
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
        .filter((page) => page.key !== "privacy" || hasPublishedPrivacyPolicy)
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
