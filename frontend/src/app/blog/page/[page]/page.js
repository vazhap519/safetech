import BlogPage from "@/app/blog/page";
import { notFound } from "next/navigation";

import { getBlog } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

function pageNumber(value) {
  return /^\d+$/.test(String(value)) ? Number(value) : null;
}

export async function generateMetadata({ params }) {
  const { page } = await params;
  const currentPage = pageNumber(page);
  const [locale, settings] = await Promise.all([
    getCurrentLocale(),
    getSiteSettings(),
  ]);
  const path = `/blog/page/${currentPage || page}`;

  if (!currentPage || currentPage < 2) {
    return createMetadata({
      title: "Invalid blog page",
      description: "The requested blog page does not exist.",
      path,
      locale,
      noindex: true,
    });
  }

  const response = await getBlog({ page: currentPage, locale });
  const lastPage = Number(response?.meta?.last_page || 0);
  const blogTitle = translateText(settings.translations, "blog.title", locale, {
    ka: "ბლოგი",
    en: "Blog",
    ru: "Блог",
  });

  return createMetadata({
    title: `${blogTitle} - ${currentPage}`,
    description: `${blogTitle}, ${currentPage}`,
    path,
    locale,
    siteName: settings.branding.siteName,
    noindex: !lastPage || currentPage > lastPage,
  });
}

export default async function BlogPaginatedPage({ params, searchParams }) {
  const resolvedParams = await params;
  const resolvedSearchParams = await searchParams;
  const currentPage = pageNumber(resolvedParams.page);

  if (!currentPage || currentPage < 2) notFound();

  const locale = await getCurrentLocale();
  const response = await getBlog({ page: currentPage, locale });
  const lastPage = Number(response?.meta?.last_page || 0);

  if (!lastPage || currentPage > lastPage) notFound();

  return (
    <BlogPage
      searchParams={{
        ...resolvedSearchParams,
        page: currentPage,
      }}
    />
  );
}
