import {
  categorySitemapResponse,
  isIndexableProject,
} from "@/lib/sitemap";
import { styleSitemapResponse } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  const response = await categorySitemapResponse({
    endpoint: "/project-categories",
    pathPrefix: "/projects/category",
    priority: "0.6",
    contentEndpoint: "/projects",
    contentFilter: isIndexableProject,
    categorySlug: (project) => project?.category,
  });

  return styleSitemapResponse(response);
}
