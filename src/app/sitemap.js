import { getBaseUrl } from "@/lib/config";

export default async function sitemap() {
  const baseUrl = getBaseUrl();

  try {
    // 🔥 fetch services (API-დან, არა static)
    const servicesRes = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/services`,
      {
        next: { revalidate: 3600 }, // 1 საათში ერთხელ
      }
    );

    const servicesData = await servicesRes.json();
    const services = servicesData?.data || [];

    // 🔥 fetch blog sitemap endpoint (ყველა პოსტი)
    const blogRes = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/sitemap/blog`,
      {
        next: { revalidate: 3600 },
      }
    );

    const posts = await blogRes.json();

    const now = new Date();

    /*
    |--------------------------------------------------------------------------
    | STATIC PAGES
    |--------------------------------------------------------------------------
    */
    const staticPages = [
      { url: baseUrl, lastModified: now },
      { url: `${baseUrl}/about`, lastModified: now },
      { url: `${baseUrl}/services`, lastModified: now },
      { url: `${baseUrl}/blog`, lastModified: now },
      { url: `${baseUrl}/contact`, lastModified: now },
    ];

    /*
    |--------------------------------------------------------------------------
    | SERVICES
    |--------------------------------------------------------------------------
    */
    const serviceUrls = services.map((service) => ({
      url: `${baseUrl}/services/${service.slug}`,
      lastModified: now,
    }));

    /*
    |--------------------------------------------------------------------------
    | BLOG POSTS
    |--------------------------------------------------------------------------
    */
    const blogUrls = posts.map((post) => ({
      url: `${baseUrl}/blog/${post.slug}`,
      lastModified: new Date(post.updated_at),
    }));

    /*
    |--------------------------------------------------------------------------
    | FINAL
    |--------------------------------------------------------------------------
    */
    return [
      ...staticPages,
      ...serviceUrls,
      ...blogUrls,
    ];
  } catch (error) {
    console.error("Sitemap error:", error);

    // fallback (ძალიან მნიშვნელოვანია)
    return [
      {
        url: baseUrl,
        lastModified: new Date(),
      },
    ];
  }
}