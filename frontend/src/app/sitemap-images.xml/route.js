import { backendAssetUrl, fetchAllPaginated, imageUrlset, normalizeBaseUrl, xmlResponse } from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  const baseUrl = normalizeBaseUrl();
  const [services, projects, posts] = await Promise.all([
    fetchAllPaginated("/services"),
    fetchAllPaginated("/projects"),
    fetchAllPaginated("/blog"),
  ]);

  const items = [
    ...services.filter((service) => !service?.seo?.noindex).map((service) => ({
      loc: `${baseUrl}/services/${service.slug}`,
      image: backendAssetUrl(service.image),
      title: service.title || service.name,
    })),
    ...projects.filter((project) => !project?.seo?.noindex).map((project) => ({
      loc: `${baseUrl}/projects/${project.slug}`,
      image: backendAssetUrl(project.image),
      title: project.title || project.name,
    })),
    ...posts.filter((post) => !post?.meta?.noindex).map((post) => ({
      loc: `${baseUrl}/blog/${post.slug}`,
      image: backendAssetUrl(post.image),
      title: post.title,
    })),
  ];

  return xmlResponse(imageUrlset(items));
}
