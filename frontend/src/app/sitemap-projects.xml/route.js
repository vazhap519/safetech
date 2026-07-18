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
  const projects = await fetchAllPaginated("/projects");
  const firstPage = await safeFetchJson(buildSitemapApiUrl("/projects", { page: 1 }));
  const totalPages = getLastPage(firstPage);
  const urls = projects.flatMap((project) => localizedUrlEntries(`/projects/${project.slug}`, {
    lastmod: project.updated_at || project.published_at || now,
    changefreq: "weekly",
    priority: "0.7",
  }));

  for (let page = 2; page <= totalPages; page += 1) {
    urls.push(...localizedUrlEntries(`/projects/page/${page}`, {
      lastmod: now,
      changefreq: "weekly",
      priority: "0.5",
    }));
  }

  return xmlResponse(urlset(urls));
}
