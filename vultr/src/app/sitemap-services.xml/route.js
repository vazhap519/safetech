import { getBaseUrl } from "@/lib/config";

export const dynamic = "force-dynamic";

/* =========================
   🔒 SAFE FETCH
========================= */
const safeFetch = async (url) => {
  try {
    const res = await fetch(url, { cache: "no-store" });

    if (!res.ok) {
      console.warn("❌ API ERROR:", url);
      return null;
    }

    return await res.json();
  } catch (e) {
    console.warn("❌ FETCH FAILED:", url);
    return null;
  }
};

export async function GET() {
  const baseUrl = getBaseUrl();
  const now = new Date().toISOString();

  try {
    const json = await safeFetch(
      `${process.env.NEXT_PUBLIC_API_URL}/services`
    );

    // 🔥 შენი API structure:
    // data: { services: [] }
    const services = json?.data?.services || [];

    /* =========================
       🔥 XML GENERATION
    ========================= */
    const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${services
  .map(
    (s) => `
  <url>
    <loc>${baseUrl}/services/${s.slug}</loc>
    <lastmod>${new Date(s.updated_at || now).toISOString()}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>`
  )
  .join("")}
</urlset>`;

    return new Response(xml, {
      headers: { "Content-Type": "application/xml" },
    });
  } catch (e) {
    console.error("❌ SERVICES SITEMAP ERROR:", e);

    return new Response(
      `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>`,
      {
        headers: { "Content-Type": "application/xml" },
      }
    );
  }
}