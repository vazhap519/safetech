import ProjectsPage from "@/app/projects/page";
import { getBaseUrl } from "@/lib/config";

export async function generateMetadata({ params }) {
  const { page } = await params;
  const currentPage = Number(page) || 1;

  return {
    alternates: {
      canonical:
        currentPage === 1
          ? `${getBaseUrl()}/projects`
          : `${getBaseUrl()}/projects/page/${currentPage}`,
    },
    robots: {
      index: currentPage <= 5,
      follow: true,
    },
  };
}

export default async function ProjectsPaginatedPage({ params, searchParams }) {
  const resolvedParams = await params;
  const resolvedSearchParams = await searchParams;

  return (
    <ProjectsPage
      searchParams={{
        ...resolvedSearchParams,
        page: Number(resolvedParams.page),
      }}
    />
  );
}
