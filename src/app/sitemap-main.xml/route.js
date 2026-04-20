import { normalizeBaseUrl, urlset, xmlResponse } from "@/lib/sitemap";

export const revalidate = 300;

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const now = new Date().toISOString();

  return xmlResponse(
    urlset([
      { loc: baseUrl, lastmod: now, changefreq: "daily", priority: "1.0" },
      { loc: `${baseUrl}/about`, lastmod: now, changefreq: "monthly", priority: "0.6" },
      { loc: `${baseUrl}/services`, lastmod: now, changefreq: "weekly", priority: "0.9" },
      { loc: `${baseUrl}/blog`, lastmod: now, changefreq: "weekly", priority: "0.7" },
      { loc: `${baseUrl}/projects`, lastmod: now, changefreq: "weekly", priority: "0.7" },
      { loc: `${baseUrl}/contact`, lastmod: now, changefreq: "monthly", priority: "0.5" },
      { loc: `${baseUrl}/privacy`, lastmod: now, changefreq: "yearly", priority: "0.2" },
    ])
  );
}
