import {
  categorySitemapResponse,
  isIndexableProduct,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  return categorySitemapResponse({
    endpoint: "/product-categories",
    pathPrefix: "/shop/category",
    priority: "0.6",
    contentEndpoint: "/products",
    contentFilter: isIndexableProduct,
    categorySlug: (product) => product?.category?.slug,
  });
}
