import {
  buildSitemapApiUrl,
  fetchPaginatedPages,
  isIndexableBlogPost,
  isIndexableCategory,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const categoriesRes = await safeFetchJson(buildSitemapApiUrl("/categories"));

  if (!categoriesRes) {
    throw new Error("Unable to load blog categories for the sitemap");
  }

  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data || [];
  const urls = [];

  for (const category of categories.filter(isIndexableCategory)) {
    const categoryBlog = await fetchPaginatedPages("/blog", {
      category: category.slug,
    });
    const indexablePages = categoryBlog.pages.filter((page) =>
      page.items.some(isIndexableBlogPost)
    );

    if (!indexablePages.length) continue;

    const encodedSlug = encodeURIComponent(category.slug);
    urls.push(...localizedUrlEntries(`/blog/category/${encodedSlug}`, {
      ...(category.updated_at ? { lastmod: category.updated_at } : {}),
      changefreq: "weekly",
      priority: "0.6",
    }));

    for (const page of indexablePages.filter((item) => item.page >= 2)) {
      urls.push(...localizedUrlEntries(`/blog/category/${encodedSlug}/page/${page.page}`, {
        changefreq: "weekly",
        priority: "0.4",
      }));
    }
  }

  return xmlResponse(urlset(urls));
}
