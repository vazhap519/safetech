import {
  fetchAllPaginated,
  isIndexableProduct,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const products = await fetchAllPaginated("/products");
  const urls = products
    .filter(isIndexableProduct)
    .flatMap((product) => localizedUrlEntries(`/shop/${encodeURIComponent(product.slug)}`, {
      ...(product.updated_at ? { lastmod: product.updated_at } : {}),
      changefreq: "weekly",
      priority: "0.7",
    }));

  return xmlResponse(urlset(urls));
}
