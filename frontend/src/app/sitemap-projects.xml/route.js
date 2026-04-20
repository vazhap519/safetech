import { fetchAllPaginated, normalizeBaseUrl, safeFetchJson, urlset, xmlResponse } from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const now = new Date().toISOString();
  const projects = await fetchAllPaginated("/projects");
  const firstPage = await safeFetchJson(`${process.env.NEXT_PUBLIC_API_URL}/projects?page=1`);
  const totalPages = Number(firstPage?.meta?.last_page || 1);
  const urls = projects.map((project) => ({
    loc: `${baseUrl}/projects/${project.slug}`,
    lastmod: project.updated_at || project.published_at || now,
    changefreq: "weekly",
    priority: "0.7",
  }));

  for (let page = 2; page <= totalPages; page += 1) {
    urls.push({
      loc: `${baseUrl}/projects/page/${page}`,
      lastmod: now,
      changefreq: "weekly",
      priority: "0.5",
    });
  }

  return xmlResponse(urlset(urls));
}
