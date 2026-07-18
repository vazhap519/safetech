import {
  buildSitemapApiUrl,
  fetchAllPaginated,
  getLastPage,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const now = new Date().toISOString();
  const services = await fetchAllPaginated("/services");
  const firstPage = await safeFetchJson(buildSitemapApiUrl("/services", { page: 1 }));
  const totalPages = getLastPage(firstPage);

  const urls = [
    ...services.flatMap((service) => localizedUrlEntries(`/services/${service.slug}`, {
      lastmod: service.updated_at || now,
      changefreq: "weekly",
      priority: "0.8",
    })),
  ];

  for (let page = 2; page <= totalPages; page += 1) {
    urls.push(...localizedUrlEntries(`/services/page/${page}`, {
      lastmod: now,
      changefreq: "weekly",
      priority: "0.5",
    }));
  }

  return xmlResponse(urlset(urls));
}
