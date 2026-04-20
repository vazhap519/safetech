import { fetchAllPaginated, normalizeBaseUrl, safeFetchJson, urlset, xmlResponse } from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const now = new Date().toISOString();
  const services = await fetchAllPaginated("/services");
  const firstPage = await safeFetchJson(`${process.env.NEXT_PUBLIC_API_URL}/services?page=1`);
  const totalPages = Number(firstPage?.data?.meta?.last_page || 1);

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
