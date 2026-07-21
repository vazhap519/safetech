import BlogPage from "@/app/blog/page";
import { notFound } from "next/navigation";

import { getCategoryPageData } from "@/lib/category-data";
import { keywordValues } from "@/lib/categorySeo";
import { getBlog } from "@/lib/datafetch";
import {
  invalidPaginationCopy,
  paginatedDescription,
  paginatedTitle,
  parsePageNumber,
} from "@/lib/pagination";
import { createMetadata } from "@/lib/seo";

export async function generateMetadata({ params }) {
  const { slug, page } = await params;
  const currentPage = parsePageNumber(page);
  const { category, locale, path: categoryPath } = await getCategoryPageData("blog", slug);
  const path = `${categoryPath}/page/${currentPage || page}`;

  if (!category || !currentPage || currentPage < 2) {
    const copy = invalidPaginationCopy(locale);

    return createMetadata({
      ...copy,
      path,
      locale,
      noindex: true,
    });
  }

  const response = await getBlog({ page: currentPage, category: slug, locale });
  const lastPage = Number(response?.meta?.last_page || 0);

  const subject = category.seo_title || category.name;

  return createMetadata({
    title: paginatedTitle(subject, currentPage, locale),
    description: paginatedDescription(subject, currentPage, locale),
    keywords: keywordValues(category),
    path,
    locale,
    noindex: Boolean(category.noindex) || !lastPage || currentPage > lastPage,
  });
}

export default async function BlogCategoryPaginatedPage({ params }) {
  const { slug, page } = await params;
  const currentPage = parsePageNumber(page);
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
