import {
  categorySitemapResponse,
  isIndexableProject,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  return categorySitemapResponse({
    endpoint: "/project-categories",
    pathPrefix: "/projects/category",
    priority: "0.6",
    contentEndpoint: "/projects",
    contentFilter: isIndexableProject,
    categorySlug: (project) => project?.category,
  });
}
