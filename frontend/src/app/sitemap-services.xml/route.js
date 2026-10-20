import {
  fetchAllPaginated,
  isIndexableService,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

const CANONICAL_SERVICE_SLUGS = new Set([
  "security-camera-installation",
  "low-voltage-cabling",
  "barrier-gate-installation",
  "intercom-access-control-installation",
  "business-it-support",
  "network-installation-configuration",
  "operating-system-installation"
]);

export const dynamic = "force-dynamic";

export async function GET() {
  const services = await fetchAllPaginated("/services", { view: "sitemap" });
  const urls = services
    .filter((service) => isIndexableService(service) && CANONICAL_SERVICE_SLUGS.has(service.slug))
    .flatMap((service) => localizedUrlEntries(`/services/${encodeURIComponent(service.slug)}`, {
      ...(service.updated_at ? { lastmod: service.updated_at } : {}),
      changefreq: "weekly",
      priority: "0.8",
    }));

  return xmlResponse(addSitemapStylesheet(urlset(urls)));
}
