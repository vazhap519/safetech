import {
  buildSitemapApiUrl,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const categoriesRes = await safeFetchJson(buildSitemapApiUrl("/service-categories"));
  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data || [];
  const urls = categories
    .filter((category) => category?.slug && !category?.noindex)
    .flatMap((category) => localizedUrlEntries(`/services/category/${category.slug}`, {
      ...(category.updated_at ? { lastmod: category.updated_at } : {}),
      changefreq: "weekly",
      priority: "0.7",
    }));

  return xmlResponse(urlset(urls));
}
