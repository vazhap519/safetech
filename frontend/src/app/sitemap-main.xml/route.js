import { getBaseUrl } from "@/lib/config";

export async function GET() {
  const baseUrl = getBaseUrl();
  const now = new Date().toISOString();

  const pages = [
    "",
    "/about",
    "/services",
    "/blog",
    "/projects",
    "/contact",
  ];

  const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${pages
  .map(
    (p) => `
  <url>
    <loc>${baseUrl}${p}</loc>
    <lastmod>${now}</lastmod>
  </url>`
  )
  .join("")}
</urlset>`;

  return new Response(xml, {
    headers: { "Content-Type": "application/xml" },
  });
}