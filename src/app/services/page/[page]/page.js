import ServicesPage from "@/app/services/page";
import { getBaseUrl } from "@/lib/config";

export async function generateMetadata({ params }) {
  const { page } = await params;
  const currentPage = Number(page) || 1;

  return {
    alternates: {
      canonical:
        currentPage === 1
          ? `${getBaseUrl()}/services`
          : `${getBaseUrl()}/services/page/${currentPage}`,
    },
    robots: {
      index: currentPage <= 5,
      follow: true,
    },
  };
}

export default async function PaginatedPage({ params, searchParams }) {
  const resolvedParams = await params;
  const resolvedSearchParams = await searchParams;
  const page = Number(resolvedParams.page) || 1;

  return (
    <ServicesPage
      searchParams={{
        ...resolvedSearchParams,
        page,
      }}
    />
  );
}
