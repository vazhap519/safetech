import {
  buildSitemapApiUrl,
  fetchAllPaginated,
  getLastPage,
  localizedUrlEntries,
  safeFetchJson,
  urlset,
  xmlResponse,
} from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const now = new Date().toISOString();
  const posts = await fetchAllPaginated("/blog");
  const firstPage = await safeFetchJson(buildSitemapApiUrl("/blog", { page: 1 }));
  const totalPages = getLastPage(firstPage);
  const urls = posts
    .filter((post) => post?.slug && !post?.meta?.noindex)
    .flatMap((post) => localizedUrlEntries(`/blog/${post.slug}`, {
      lastmod: post.updated_at || post.created_at || now,
      changefreq: "weekly",
      priority: "0.7",
    }));

  for (let page = 2; page <= totalPages; page += 1) {
    urls.push(...localizedUrlEntries(`/blog/page/${page}`, {
      changefreq: "weekly",
      priority: "0.5",
    }));
  }

  return xmlResponse(urlset(urls));
}
