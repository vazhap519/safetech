import {
  fetchAllPaginated,
  isIndexableProject,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const now = new Date().toISOString();
  const projects = await fetchAllPaginated("/projects");
  const urls = projects
    .filter(isIndexableProject)
    .flatMap((project) => localizedUrlEntries(`/projects/${encodeURIComponent(project.slug)}`, {
      lastmod: project.updated_at || project.publishedAt || now,
      changefreq: "weekly",
      priority: "0.7",
    }));

  return xmlResponse(urlset(urls));
}
