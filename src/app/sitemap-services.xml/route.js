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
  const now = new Date().toISOString();

  const json = await safeFetch(
    `${process.env.NEXT_PUBLIC_API_URL}/services`
  );

  const services = json?.data?.services || [];
  const meta = json?.data?.meta || {};
  const totalPages = meta?.last_page || 1;

  const categoriesRes = await safeFetch(
    `${process.env.NEXT_PUBLIC_API_URL}/service-categories`
  );

  const categories = categoriesRes || [];

  let urls = [];

  /* =========================
     🔥 SERVICES (single)
  ========================= */
  services.forEach((s) => {
    urls.push(`
     <url>
    <loc>${baseUrl}/services/${s.slug}</loc>
    <lastmod>${now}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
    `);
  });

  /* =========================
     🔥 PAGINATION
  ========================= */
  for (let i = 2; i <= totalPages; i++) {
    urls.push(`
      <url>
        <loc>${baseUrl}/services/page/${i}</loc>
        <lastmod>${now}</lastmod>
      </url>
    `);
  }

  /* =========================
     🔥 CATEGORY + PAGINATION
  ========================= */
categories.forEach((cat) => {
  urls.push(`
    <url>
      <loc>${baseUrl}/services/category/${cat.slug}</loc>
      <lastmod>${now}</lastmod>
    </url>
  `);

  // 🔥 FETCH CATEGORY SERVICES
  const categoryData = json?.data?.services?.filter(
    (s) => s.category?.slug === cat.slug
  ) || [];

  const perPage = meta?.per_page || 6;
  const catTotalPages = Math.ceil(categoryData.length / perPage);

  for (let i = 2; i <= catTotalPages; i++) {
    urls.push(`
      <url>
        <loc>${baseUrl}/services/category/${cat.slug}/page/${i}</loc>
        <lastmod>${now}</lastmod>
      </url>
    `);
  }
});
  const xml = `<?xml version="1.0" encoding="UTF-8"?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    ${urls.join("")}
  </urlset>`;

  return new Response(xml, {
    headers: { "Content-Type": "application/xml" },
  });
}