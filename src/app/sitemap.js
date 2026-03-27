// import { getBaseUrl } from "@/lib/config";

// export default async function sitemap() {
//   const baseUrl = getBaseUrl();

//   try {
//     const servicesRes = await fetch(
//       `${process.env.NEXT_PUBLIC_API_URL}/services`,
//       { next: { revalidate: 3600 } }
//     );

//     const servicesData = await servicesRes.json();
//     const services = servicesData?.services || [];

//     const blogRes = await fetch(
//       `${process.env.NEXT_PUBLIC_API_URL}/sitemap/blog`,
//       { next: { revalidate: 3600 } }
//     );

//     const posts = await blogRes.json();

//     const now = new Date();

//     return [
//       // 🔹 STATIC
//       { url: baseUrl, lastModified: now },
//       { url: `${baseUrl}/about`, lastModified: now },
//       { url: `${baseUrl}/services`, lastModified: now },
//       { url: `${baseUrl}/blog`, lastModified: now },
//       { url: `${baseUrl}/contact`, lastModified: now },

//       // 🔹 SERVICES
//       ...services.map((s) => ({
//         url: `${baseUrl}/services/${s.slug}`,
//         lastModified: now,
//       })),

//       // 🔹 BLOG
//       ...posts.map((p) => ({
//         url: `${baseUrl}/blog/${p.slug}`,
//         lastModified: new Date(p.updated_at),
//       })),
//     ];
//   } catch (e) {
//     return [
//       {
//         url: baseUrl,
//         lastModified: new Date(),
//       },
//     ];
//   }
// }


// import { getBaseUrl } from "@/lib/config";

// export default async function sitemap() {
//   const baseUrl = getBaseUrl();

//   try {
//     const servicesRes = await fetch(
//       `${process.env.NEXT_PUBLIC_API_URL}/services`,
//       { next: { revalidate: 3600 } }
//     );

//     const servicesData = await servicesRes.json();
//     const services = servicesData?.services || [];

//     const blogRes = await fetch(
//       `${process.env.NEXT_PUBLIC_API_URL}/sitemap/blog`,
//       { next: { revalidate: 3600 } }
//     );

//     const posts = await blogRes.json();

//     const now = new Date();

//     return [
//       // 🔹 STATIC
//       { url: baseUrl, lastModified: now },
//       { url: `${baseUrl}/about`, lastModified: now },
//       { url: `${baseUrl}/services`, lastModified: now },
//       { url: `${baseUrl}/blog`, lastModified: now },
//       { url: `${baseUrl}/contact`, lastModified: now },

//       // 🔹 SERVICES
//       ...services.map((s) => ({
//         url: `${baseUrl}/services/${s.slug}`,
//         lastModified: now,
//       })),

//       // 🔹 BLOG
//       ...posts.map((p) => ({
//         url: `${baseUrl}/blog/${p.slug}`,
//         lastModified: new Date(p.updated_at),
//       })),
//     ];
//   } catch (e) {
//     return [
//       {
//         url: baseUrl,
//         lastModified: new Date(),
//       },
//     ];
//   }
// }


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
      fetch(`${process.env.NEXT_PUBLIC_API_URL}/sitemap/blog`, {
        next: { revalidate: 3600 },
      }),
    ]);

    const seoJson = await seoRes.json();
    const servicesJson = await servicesRes.json();
    const posts = await blogRes.json();

    const seoPages = seoJson?.data || [];
    const services = servicesJson?.services || [];

    const now = new Date();

    /*
    |--------------------------------------------------------------------------
    | 🔥 STATIC (FROM SEO SYSTEM)
    |--------------------------------------------------------------------------
    */
    const staticUrls = seoPages.map((p) => ({
      url: `${baseUrl}${p.slug || ""}`,
      lastModified: new Date(p.updated_at || now),
    }));

    /*
    |--------------------------------------------------------------------------
    | 🔥 SERVICES
    |--------------------------------------------------------------------------
    */
    const serviceUrls = services.map((s) => ({
      url: `${baseUrl}/services/${s.slug}`,
      lastModified: now,
    }));

    /*
    |--------------------------------------------------------------------------
    | 🔥 BLOG
    |--------------------------------------------------------------------------
    */
    const blogUrls = posts.map((p) => ({
      url: `${baseUrl}/blog/${p.slug}`,
      lastModified: new Date(p.updated_at),
    }));

    return [
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