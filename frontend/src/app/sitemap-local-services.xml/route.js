import {
  buildSitemapApiUrl,
  hasMeaningfulContent,
  hasValidSitemapSlug,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  const response = await safeFetchJson(buildSitemapApiUrl("/local-service-landings"));
  const landings = Array.isArray(response?.data) ? response.data : [];
  const urls = landings
    .filter((landing) => (
      hasValidSitemapSlug(landing?.service?.slug)
      && hasValidSitemapSlug(landing?.locationSlug)
      && !landing?.seo?.noindex
      && hasMeaningfulContent(landing?.title)
      && hasMeaningfulContent(landing?.content)
    ))
    .flatMap((landing) => localizedUrlEntries(
      `/services/${encodeURIComponent(landing.service.slug)}/${encodeURIComponent(landing.locationSlug)}`,
      {
        ...(landing.updated_at ? { lastmod: landing.updated_at } : {}),
        changefreq: "weekly",
        priority: "0.8",
      },
    ));

  return xmlResponse(addSitemapStylesheet(urlset(urls)));
}
