import { getBaseUrl } from "@/lib/config";

export const dynamic = "force-dynamic";

const safeFetch = async (url) => {
  try {
    const res = await fetch(url, { cache: "no-store" });
    if (!res.ok) return null;
    return await res.json();
  } catch {
    return null;
  }
};

export async function GET() {
  const baseUrl = getBaseUrl();

  const [blogCatJson, projectCatJson] = await Promise.all([
    safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/categories`),
    safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/project-categories`),
  ]);

  const blogCats = blogCatJson?.data || [];
  const projectCats = projectCatJson?.data || [];

  const items = [
    ...blogCats.map((c) => ({
      url: `${baseUrl}/blog?category=${c.slug}`,
    })),
    ...projectCats.map((c) => ({
      url: `${baseUrl}/projects?category=${c.slug}`,
    })),
  ];

  const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${items
  .map(
    (i) => `
  <url>
    <loc>${i.url}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>`
  )
  .join("")}
</urlset>`;

  return new Response(xml, {
    headers: { "Content-Type": "application/xml" },
  });
}