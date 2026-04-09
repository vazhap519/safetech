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

/* =========================
   🔥 FETCH ALL PROJECT PAGES
========================= */
async function fetchAllProjects() {
  let page = 1;
  let all = [];

  while (true) {
    const json = await safeFetch(
      `${process.env.NEXT_PUBLIC_API_URL}/projects?page=${page}`
    );

    const projects = json?.data || [];
    const lastPage = json?.meta?.last_page || 1;

    all.push(...projects);

    if (page >= lastPage) break;
    page++;
  }

  return all;
}

export async function GET() {
  const baseUrl = getBaseUrl();
  const now = new Date().toISOString();

  try {
    const projects = await fetchAllProjects();

    const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${projects
  .map(
    (p) => `
  <url>
    <loc>${baseUrl}/projects/${p.slug}</loc>
    <lastmod>${new Date(p.updated_at || now).toISOString()}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>`
  )
  .join("")}
</urlset>`;

    return new Response(xml, {
      headers: { "Content-Type": "application/xml" },
    });
  } catch (e) {
    console.error("❌ PROJECTS SITEMAP ERROR:", e);

    return new Response(
      `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>`,
      {
        headers: { "Content-Type": "application/xml" },
      }
    );
  }
}