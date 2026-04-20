import { getBaseUrl } from "@/lib/config";

export default function robots() {
  const baseUrl = getBaseUrl().replace(/\/$/, "");

  return {
    rules: [
      {
        userAgent: "*",
        allow: "/",
        disallow: [
          "/api/",
          "/admin/",
          "/_next/",
          "/login",
          "/register",
          "/search",
          "/*?*",
        ],
      },
    ],
    sitemap: [
      `${baseUrl}/sitemap.xml`,
      `${baseUrl}/sitemap-main.xml`,
      `${baseUrl}/sitemap-services.xml`,
      `${baseUrl}/sitemap-service-categories.xml`,
      `${baseUrl}/sitemap-blog.xml`,
      `${baseUrl}/sitemap-blog-categories.xml`,
      `${baseUrl}/sitemap-projects.xml`,
      `${baseUrl}/sitemap-project-categories.xml`,
      `${baseUrl}/sitemap-images.xml`,
    ],
    host: baseUrl,
  };
}
