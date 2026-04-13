import { getBaseUrl } from "@/lib/config";

export async function GET() {
  const baseUrl = getBaseUrl();

  return new Response(`<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap><loc>${baseUrl}/sitemap-main.xml</loc></sitemap>
  <sitemap><loc>${baseUrl}/sitemap-services.xml</loc></sitemap>
  <sitemap><loc>${baseUrl}/sitemap-blog.xml</loc></sitemap>
  <sitemap><loc>${baseUrl}/sitemap-projects.xml</loc></sitemap>
  <sitemap><loc>${baseUrl}sitemap-images.xml</loc></sitemap>
<sitemap><loc>${baseUrl}sitemap-categories.xml</loc></sitemap>
</sitemapindex>`, {
    headers: { "Content-Type": "application/xml" },
  });
}