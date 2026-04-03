import { getBaseUrl } from "@/lib/config";

// ❗ სრულად ვაუქმებთ cache-ს (სწორი გზა dynamic sitemap-ზე)
export const dynamic = "force-dynamic";

export default async function sitemap() {
  const baseUrl = getBaseUrl();
  const now = new Date();

  // ✅ safe fetch helper
  const safeFetch = async (url) => {
    try {
      const res = await fetch(url, {
        cache: "no-store", // ❗ კრიტიკული
      });

      if (!res.ok) {
        console.warn("❌ API ERROR:", url);
        return { data: [] };
      }

      return await res.json();
    } catch (e) {
      console.warn("❌ FETCH FAILED:", url);
      return { data: [] };
    }
  };

  try {
    // ✅ პარალელური fetch
    const [seoJson, servicesJson, blogJson, projectsJson] =
      await Promise.all([
        safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/seo`),
        safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/services`),
        safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/blog?per_page=100`),
        safeFetch(`${process.env.NEXT_PUBLIC_API_URL}/projects?per_page=100`),
      ]);

    const seoPages = seoJson?.data || [];
    const services = servicesJson?.data || [];
    const posts = blogJson?.data || [];
    const projects = projectsJson?.data || [];

    /* =========================
       🔥 MAIN
    ========================= */
    const mainUrls = [
      { url: baseUrl, lastModified: now },
      { url: `${baseUrl}/about`, lastModified: now },
      { url: `${baseUrl}/privacy`, lastModified: now },
      { url: `${baseUrl}/contact`, lastModified: now },
      { url: `${baseUrl}/services`, lastModified: now },
      { url: `${baseUrl}/blog`, lastModified: now },
      { url: `${baseUrl}/projects`, lastModified: now },
    ];

    /* =========================
       🔥 SEO STATIC
    ========================= */
    const staticUrls = seoPages.map((p) => ({
      url: `${baseUrl}${p.slug || ""}`,
      lastModified: new Date(p.updated_at || now),
    }));

    /* =========================
       🔥 SERVICES
    ========================= */
    const serviceUrls = services.map((s) => ({
      url: `${baseUrl}/services/${s.slug}`,
      lastModified: new Date(s.updated_at || now), // ❗ fixed
    }));

    /* =========================
       🔥 BLOG
    ========================= */
    const blogUrls = posts.map((p) => ({
      url: `${baseUrl}/blog/${p.slug}`,
      lastModified: new Date(p.updated_at || now),
    }));

    /* =========================
       🔥 PROJECTS
    ========================= */
    const projectUrls = projects.map((p) => ({
      url: `${baseUrl}/projects/${p.slug}`,
      lastModified: new Date(p.updated_at || now),
    }));

    return [
      ...mainUrls,
      ...staticUrls,
      ...serviceUrls,
      ...blogUrls,
      ...projectUrls,
    ];
  } catch (e) {
    console.error("❌ Sitemap error:", e);

    return [
      {
        url: baseUrl,
        lastModified: now,
      },
    ];
  }
}