import { getBaseUrl } from "@/lib/config";

export default async function sitemap() {
  const baseUrl = getBaseUrl();

  try {
    const [seoRes, servicesRes, blogRes] = await Promise.all([
      fetch(`${process.env.NEXT_PUBLIC_API_URL}/seo`, {
        next: { revalidate: 3600 },
      }),
      fetch(`${process.env.NEXT_PUBLIC_API_URL}/services`, {
        next: { revalidate: 3600 },
      }),
      fetch(`${process.env.NEXT_PUBLIC_API_URL}/blog`, {
        next: { revalidate: 3600 },
      }),
    ]);

    const seoJson = await seoRes.json();
    const servicesJson = await servicesRes.json();
    const blogJson = await blogRes.json();

    const seoPages = seoJson?.data || [];
    const services = servicesJson?.services || [];
    const posts = blogJson?.data || [];

    const now = new Date();

    /*
    | 🔥 MAIN PAGES
    */
    const mainUrls = [
      { url: baseUrl, lastModified: now },
      { url: `${baseUrl}/about`, lastModified: now },
      { url: `${baseUrl}/privacy`, lastModified: now },
      { url: `${baseUrl}/contact`, lastModified: now },
      { url: `${baseUrl}/services`, lastModified: now },
      { url: `${baseUrl}/blog`, lastModified: now },
    ];

    /*
    | 🔥 SEO (dynamic pages)
    */
    const staticUrls = seoPages.map((p) => ({
      url: `${baseUrl}${p.slug || ""}`,
      lastModified: new Date(p.updated_at || now),
    }));

    /*
    | 🔥 SERVICES (dynamic)
    */
    const serviceUrls = services.map((s) => ({
      url: `${baseUrl}/services/${s.slug}`,
      lastModified: now,
      changeFrequency: "weekly",
      priority: 0.9,
    }));

    /*
    | 🔥 BLOG (dynamic)
    */
    const blogUrls = posts.map((p) => ({
      url: `${baseUrl}/blog/${p.slug}`,
      lastModified: new Date(p.updated_at),
      changeFrequency: "weekly",
      priority: 0.8,
    }));

    return [
      ...mainUrls,
      ...staticUrls,
      ...serviceUrls,
      ...blogUrls,
    ];
  } catch (e) {
    return [
      {
        url: baseUrl,
        lastModified: new Date(),
      },
    ];
  }
}