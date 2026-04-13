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
  let lastPage = 1;

  while (true) {
    const json = await safeFetch(
      `${process.env.NEXT_PUBLIC_API_URL}/blog?page=${page}`
    );

    const posts = json?.data || [];
    lastPage = json?.meta?.last_page || 1;

    allPosts.push(...posts);

    if (page >= lastPage) break;
    page++;
  }

  return { posts: allPosts, lastPage };
}

export async function GET() {
  const baseUrl = getBaseUrl();
  const now = new Date().toISOString();

  try {
    const { posts, lastPage } = await fetchAllPosts();

    const categoriesRes = await safeFetch(
      `${process.env.NEXT_PUBLIC_API_URL}/categories`
    );

    const categories = categoriesRes || [];

    const urls = [];

    /* =========================
       🔥 POSTS
    ========================= */
    posts.forEach((p) => {
      urls.push(`
        <url>
          <loc>${baseUrl}/blog/${p.slug}</loc>
          <lastmod>${new Date(p.updated_at || now).toISOString()}</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.7</priority>
        </url>
      `);
    });

    /* =========================
       🔥 PAGINATION (ALL)
    ========================= */
    for (let i = 2; i <= lastPage; i++) {
      urls.push(`
        <url>
          <loc>${baseUrl}/blog/page/${i}</loc>
          <lastmod>${now}</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.6</priority>
        </url>
      `);
    }

    /* =========================
       🔥 CATEGORY + PAGINATION
    ========================= */
    categories.forEach((cat) => {
      urls.push(`
        <url>
          <loc>${baseUrl}/blog/category/${cat.slug}</loc>
          <lastmod>${now}</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.6</priority>
        </url>
      `);

      for (let i = 2; i <= lastPage; i++) {
        urls.push(`
          <url>
            <loc>${baseUrl}/blog/category/${cat.slug}/page/${i}</loc>
            <lastmod>${now}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.5</priority>
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