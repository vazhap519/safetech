import {
  categorySitemapResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  return categorySitemapResponse({
    endpoint: "/project-categories",
    pathPrefix: "/projects/category",
    priority: "0.6",
  });
}
