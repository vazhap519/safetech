import {
  categorySitemapResponse,
  isIndexableService,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  return categorySitemapResponse({
    endpoint: "/service-categories",
    pathPrefix: "/services/category",
    priority: "0.7",
    contentEndpoint: "/services",
    contentFilter: isIndexableService,
    categorySlug: (service) => service?.category?.slug,
  });
}
