import BlogPage from "@/app/blog/page";
import { notFound } from "next/navigation";

import { getCategoryPageData } from "@/lib/category-data";
import { keywordValues } from "@/lib/categorySeo";
import { getBlog } from "@/lib/datafetch";
import { createMetadata } from "@/lib/seo";

function pageNumber(value) {
  return /^\d+$/.test(String(value)) ? Number(value) : null;
}

export async function generateMetadata({ params }) {
  const { slug, page } = await params;
  const currentPage = pageNumber(page);
  const { category, locale, path: categoryPath } = await getCategoryPageData("blog", slug);
  const path = `${categoryPath}/page/${currentPage || page}`;

  if (!category || !currentPage || currentPage < 2) {
    return createMetadata({
      title: "Category page not found",
      description: "The requested category page does not exist.",
      path,
      locale,
      noindex: true,
    });
  }

  const response = await getBlog({ page: currentPage, category: slug, locale });
  const lastPage = Number(response?.meta?.last_page || 0);

  return createMetadata({
    title: `${category.seo_title || category.name} - ${currentPage}`,
    description: category.seo_description || category.intro_text || category.name,
    keywords: keywordValues(category),
    path,
    locale,
    noindex: Boolean(category.noindex) || !lastPage || currentPage > lastPage,
  });
}

export default async function BlogCategoryPaginatedPage({ params }) {
  const { slug, page } = await params;
  const currentPage = pageNumber(page);
  const { category, locale } = await getCategoryPageData("blog", slug);

  if (!category || !currentPage || currentPage < 2) notFound();

  const response = await getBlog({ page: currentPage, category: slug, locale });
  const lastPage = Number(response?.meta?.last_page || 0);

  if (!lastPage || currentPage > lastPage) notFound();

  return (
    <BlogPage
      heading={category.name}
      searchParams={{
        category: slug,
        page: currentPage,
      }}
      showPageSchema={false}
    />
  );
}
