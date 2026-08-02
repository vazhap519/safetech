import {
  categorySitemapResponse,
  isIndexableService,
} from "@/lib/sitemap";
import { styleSitemapResponse } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  const response = await categorySitemapResponse({
    endpoint: "/service-categories",
    pathPrefix: "/services/category",
    priority: "0.7",
    contentEndpoint: "/services",
    contentFilter: isIndexableService,
    categorySlug: (service) => service?.category?.slug,
  });

  return styleSitemapResponse(response);
}
