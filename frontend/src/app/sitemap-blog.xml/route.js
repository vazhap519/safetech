import {
  fetchPaginatedPages,
  isIndexableBlogPost,
  localizedUrlEntries,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const blog = await fetchPaginatedPages("/blog");
  const urls = blog.items
    .filter(isIndexableBlogPost)
    .flatMap((post) => localizedUrlEntries(`/blog/${encodeURIComponent(post.slug)}`, {
      ...(post.updated_at || post.created_at
        ? { lastmod: post.updated_at || post.created_at }
        : {}),
      changefreq: "weekly",
      priority: "0.7",
    }));

  for (const page of blog.pages.filter(
    (item) => item.page >= 2 && item.items.some(isIndexableBlogPost),
  )) {
    urls.push(...localizedUrlEntries(`/blog/page/${page.page}`, {
      changefreq: "weekly",
      priority: "0.5",
    }));
  }

  return xmlResponse(urlset(urls));
}
