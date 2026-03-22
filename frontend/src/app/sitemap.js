import { servicesList } from "@/data/services";

export default function sitemap() {
  const baseUrl = "https://safetech.ge";
  const now = new Date();

  // 🔥 Dynamic service pages
  const serviceUrls = servicesList.map((service) => ({
    url: `${baseUrl}/services/${service.slug}`,
    lastModified: now,
  }));

  return [
    {
      url: baseUrl,
      lastModified: now,
    },
    {
      url: `${baseUrl}/about`,
      lastModified: now,
    },
    {
      url: `${baseUrl}/services`,
      lastModified: now,
    },
    {
      url: `${baseUrl}/contact`,
      lastModified: now,
    },

    // 🔥 ავტომატურად ემატება ყველა სერვისი
    ...serviceUrls,
  ];
}