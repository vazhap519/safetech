import {
  buildSitemapApiUrl,
  getLastPage,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const now = new Date().toISOString();
  const categoriesRes = await safeFetchJson(buildSitemapApiUrl("/categories"));
  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data || [];
  const urls = [];

  for (const category of categories) {
    urls.push(...localizedUrlEntries(`/blog/category/${category.slug}`, {
      lastmod: now,
      changefreq: "weekly",
      priority: "0.6",
    }));

    const categoryRes = await safeFetchJson(
      buildSitemapApiUrl("/blog", { category: category.slug, page: 1 })
    );
    const lastPage = getLastPage(categoryRes);

    for (let page = 2; page <= lastPage; page += 1) {
      urls.push(...localizedUrlEntries(`/blog/category/${category.slug}/page/${page}`, {
        lastmod: now,
        changefreq: "weekly",
        priority: "0.4",
      }));
    }
  }

  return xmlResponse(urlset(urls));
}
