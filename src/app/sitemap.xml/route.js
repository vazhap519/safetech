import { sitemapIndex, xmlResponse } from "@/lib/sitemap";

export const revalidate = 300;

export async function GET() {
  return xmlResponse(
    sitemapIndex([
      "/sitemap-main.xml",
      "/sitemap-services.xml",
      "/sitemap-service-categories.xml",
      "/sitemap-blog.xml",
      "/sitemap-blog-categories.xml",
      "/sitemap-projects.xml",
      "/sitemap-project-categories.xml",
      "/sitemap-images.xml",
    ])
  );
}
