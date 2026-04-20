import ProjectsPage from "@/app/projects/page";
import { getBaseUrl } from "@/lib/config";

export async function generateMetadata({ params }) {
  const { slug, page } = await params;
  const currentPage = Number(page) || 1;

  return {
    alternates: {
      canonical:
        currentPage === 1
          ? `${getBaseUrl()}/projects/category/${slug}`
          : `${getBaseUrl()}/projects/category/${slug}/page/${currentPage}`,
    },
    robots: {
      index: currentPage <= 5,
      follow: true,
    },
  };
}

export default async function ProjectCategoryPaginatedPage({ params }) {
  const { slug, page } = await params;

  return (
    <ProjectsPage
      searchParams={{
        category: slug,
        page: Number(page),
      }}
    />
  );
}
