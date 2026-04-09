import { getBaseUrl } from "@/lib/config";

export const dynamic = "force-dynamic";

/* =========================
   🔒 SAFE FETCH
========================= */
const safeFetch = async (url) => {
  try {
    const res = await fetch(url, { cache: "no-store" });

    if (!res.ok) return null;

    return await res.json();
  } catch {
    return null;
  }
};

/* =========================
   🔥 FETCH ALL BLOG PAGES
========================= */
async function fetchAllPosts() {
  let page = 1;
  let allPosts = [];

  while (true) {
    const json = await safeFetch(
      `${process.env.NEXT_PUBLIC_API_URL}/blog?page=${page}`
    );

    const posts = json?.data || [];
    const lastPage = json?.meta?.last_page || 1;

    allPosts.push(...posts);

    if (page >= lastPage) break;
    page++;
  }

  return allPosts;
}

export async function GET() {
  const baseUrl = getBaseUrl();
  const now = new Date().toISOString();

  try {
    const posts = await fetchAllPosts();

    const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${posts
  .map(
    (p) => `
  <url>
    <loc>${baseUrl}/blog/${p.slug}</loc>
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
    console.error("❌ BLOG SITEMAP ERROR:", e);

    return new Response(
      `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>`,
      {
        headers: { "Content-Type": "application/xml" },
      }
    );
  }
}