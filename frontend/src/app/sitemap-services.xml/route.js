import {
  buildSitemapApiUrl,
  fetchAllPaginated,
  getLastPage,
  normalizeBaseUrl,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const now = new Date().toISOString();
  const services = await fetchAllPaginated("/services");
  const firstPage = await safeFetchJson(buildSitemapApiUrl("/services", { page: 1 }));
  const totalPages = getLastPage(firstPage);

  const urls = [
    ...services.map((service) => ({
      loc: `${baseUrl}/services/${service.slug}`,
      lastmod: service.updated_at || now,
      changefreq: "weekly",
      priority: "0.8",
    })),
  ];

  for (let page = 2; page <= totalPages; page += 1) {
    urls.push({
      loc: `${baseUrl}/services/page/${page}`,
      lastmod: now,
      changefreq: "weekly",
      priority: "0.5",
    });
  }

  return xmlResponse(urlset(urls));
}
