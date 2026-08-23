import {
  fetchAllPaginated,
  isIndexablePage,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

const LEGAL_PAGE_SLUGS = new Set(["privacy", "terms"]);

export async function GET() {
  const pages = await fetchAllPaginated("/pages");
  const urls = pages
    .filter(isIndexablePage)
    .flatMap((page) => {
      const path = LEGAL_PAGE_SLUGS.has(page.slug)
        ? `/${encodeURIComponent(page.slug)}`
        : `/pages/${encodeURIComponent(page.slug)}`;

      return localizedUrlEntries(path, {
        ...(page.updated_at ? { lastmod: page.updated_at } : {}),
        changefreq: "monthly",
        priority: LEGAL_PAGE_SLUGS.has(page.slug) ? "0.3" : "0.5",
      });
    });

  return xmlResponse(addSitemapStylesheet(urlset(urls)));
}
