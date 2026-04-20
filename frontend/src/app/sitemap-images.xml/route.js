import { absoluteUrl, fetchAllPaginated, imageUrlset, normalizeBaseUrl, xmlResponse } from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const [services, projects, posts] = await Promise.all([
    fetchAllPaginated("/services"),
    fetchAllPaginated("/projects"),
    fetchAllPaginated("/blog"),
  ]);

  const items = [
    ...services.map((service) => ({
      loc: `${baseUrl}/services/${service.slug}`,
      image: absoluteUrl(service.image),
    })),
    ...projects.map((project) => ({
      loc: `${baseUrl}/projects/${project.slug}`,
      image: absoluteUrl(project.image),
    })),
    ...posts.map((post) => ({
      loc: `${baseUrl}/blog/${post.slug}`,
      image: absoluteUrl(post.image),
    })),
  ];

  return xmlResponse(imageUrlset(items));
}
