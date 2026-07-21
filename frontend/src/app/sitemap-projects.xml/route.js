import {
  fetchAllPaginated,
  isIndexableProject,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const projects = await fetchAllPaginated("/projects");
  const urls = projects
    .filter(isIndexableProject)
    .flatMap((project) => localizedUrlEntries(`/projects/${encodeURIComponent(project.slug)}`, {
      ...(project.updated_at || project.publishedAt
        ? { lastmod: project.updated_at || project.publishedAt }
        : {}),
      changefreq: "weekly",
      priority: "0.7",
    }));

  return xmlResponse(urlset(urls));
}
