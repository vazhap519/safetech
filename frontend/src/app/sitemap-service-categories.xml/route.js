import {
  categorySitemapResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  return categorySitemapResponse({
    endpoint: "/service-categories",
    pathPrefix: "/services/category",
    priority: "0.7",
  });
}
