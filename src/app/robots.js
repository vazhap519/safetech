export default function robots() {
  const isProd = process.env.NODE_ENV === "production";
  const baseUrl = process.env.NEXT_PUBLIC_SITE_URL;

  return {
    rules: [
      {
        userAgent: "*",
        allow: "/",
        disallow: [
          "/admin",
          "/api",
          "/_next",
        ],
        crawlDelay: 10,
      },
    ],
    sitemap: `${baseUrl}/sitemap.xml`,
    host: baseUrl,
  };
}