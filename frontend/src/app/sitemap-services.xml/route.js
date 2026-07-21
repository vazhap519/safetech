import {
  fetchAllPaginated,
  isIndexableService,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const now = new Date().toISOString();
  const services = await fetchAllPaginated("/services");
  const urls = services
    .filter(isIndexableService)
    .flatMap((service) => localizedUrlEntries(`/services/${encodeURIComponent(service.slug)}`, {
      lastmod: service.updated_at || now,
      changefreq: "weekly",
      priority: "0.8",
    }));

  return xmlResponse(urlset(urls));
}
