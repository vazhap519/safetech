import {
  buildSitemapApiUrl,
  isIndexableLocalServiceLanding,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  const response = await safeFetchJson(buildSitemapApiUrl(
    "/local-service-landings",
    { view: "sitemap" },
  ));
  const landings = Array.isArray(response?.data) ? response.data : [];
  const urls = landings
    .filter(isIndexableLocalServiceLanding)
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
