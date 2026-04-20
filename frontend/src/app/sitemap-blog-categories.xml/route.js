import { normalizeBaseUrl, safeFetchJson, urlset, xmlResponse } from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const now = new Date().toISOString();
  const categoriesRes = await safeFetchJson(`${process.env.NEXT_PUBLIC_API_URL}/categories`);
  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data || [];
  const urls = [];

  for (const category of categories) {
    urls.push({
      loc: `${baseUrl}/blog/category/${category.slug}`,
      lastmod: now,
      changefreq: "weekly",
      priority: "0.6",
    });

    const categoryRes = await safeFetchJson(
      `${process.env.NEXT_PUBLIC_API_URL}/blog?category=${category.slug}&page=1`
    );
    const lastPage = Number(categoryRes?.meta?.last_page || 1);

    for (let page = 2; page <= lastPage; page += 1) {
      urls.push({
        loc: `${baseUrl}/blog/category/${category.slug}/page/${page}`,
        lastmod: now,
        changefreq: "weekly",
        priority: "0.4",
      });
    }
  }

  return xmlResponse(urlset(urls));
}
