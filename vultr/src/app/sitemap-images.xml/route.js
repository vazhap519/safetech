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

  const [servicesJson, projectsJson, blogJson] = await Promise.all([
    safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/services`),
    safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/projects`),
    safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/blog?per_page=100`),
  ]);

  const services = servicesJson?.data?.services || [];
  const projects = projectsJson?.data || [];
  const posts = blogJson?.data || [];

  const items = [
    ...services.map((s) => ({
      url: `${baseUrl}/services/${s.slug}`,
      image: s.image,
    })),
    ...projects.map((p) => ({
      url: `${baseUrl}/projects/${p.slug}`,
      image: p.image,
    })),
    ...posts.map((p) => ({
      url: `${baseUrl}/blog/${p.slug}`,
      image: p.image,
    })),
  ];

  const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
${items
  .filter((i) => i.image)
  .map(
    (i) => `
  <url>
    <loc>${i.url}</loc>
    <image:image>
      <image:loc>${i.image}</image:loc>
    </image:image>
  </url>`
  )
  .join("")}
</urlset>`;

  return new Response(xml, {
    headers: { "Content-Type": "application/xml" },
  });
}