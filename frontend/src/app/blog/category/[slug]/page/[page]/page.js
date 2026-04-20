import BlogPage from "@/app/blog/page";
import { getBaseUrl } from "@/lib/config";

export async function generateMetadata({ params }) {
  const { slug, page } = await params;
  const currentPage = Number(page) || 1;

  return {
    alternates: {
      canonical:
        currentPage === 1
          ? `${getBaseUrl()}/blog/category/${slug}`
          : `${getBaseUrl()}/blog/category/${slug}/page/${currentPage}`,
    },
    robots: {
      index: currentPage <= 5,
      follow: true,
    },
  };
}

export default async function BlogCategoryPaginatedPage({ params }) {
  const { slug, page } = await params;

  return (
    <BlogPage
      searchParams={{
        category: slug,
        page: Number(page),
      }}
    />
  );
}
