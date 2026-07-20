import { fetchImageSitemapItems, sitemapIndex, xmlResponse } from "@/lib/sitemap";

export const revalidate = 300;

export async function GET() {
  const imageItems = await fetchImageSitemapItems();

  return xmlResponse(
    sitemapIndex([
      "/sitemap-main.xml",
      "/sitemap-services.xml",
      "/sitemap-service-categories.xml",
      "/sitemap-blog.xml",
      "/sitemap-blog-categories.xml",
      "/sitemap-projects.xml",
      "/sitemap-project-categories.xml",
      ...(imageItems.length ? ["/sitemap-images.xml"] : []),
    ])
  );
}
