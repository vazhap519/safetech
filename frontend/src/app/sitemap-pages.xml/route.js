import {
  fetchAllPaginated,
  isIndexablePage,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  const pages = await fetchAllPaginated("/pages");
  const urls = pages
    .filter(isIndexablePage)
    .flatMap((page) => localizedUrlEntries(`/pages/${encodeURIComponent(page.slug)}`, {
      ...(page.updated_at ? { lastmod: page.updated_at } : {}),
      changefreq: "monthly",
      priority: "0.5",
    }));

  return xmlResponse(addSitemapStylesheet(urlset(urls)));
}
