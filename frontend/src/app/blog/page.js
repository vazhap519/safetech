import Link from "next/link";
import { redirect } from "next/navigation";

import BlogList from "@/components/blog/BlogList";
import BlogSchema from "@/components/seo/BlogSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { getBlog, getCategories } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import { firstSearchParam, parsePageNumber } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export async function generateMetadata() {
  return createCmsPageMetadata(PAGE_SEO_PRESETS.blog);
}

export default async function BlogPage({ heading, searchParams, showPageSchema = true }) {
  const params = await searchParams;
  const category = firstSearchParam(params?.category) || "all";
  const parsedPage = parsePageNumber(params?.page);
  const currentPage = parsedPage || 1;
  const [locale, { translations }] = await Promise.all([
    getCurrentLocale(),
    getSiteSettings(),
  ]);

  if (showPageSchema && (params?.category !== undefined || params?.page !== undefined)) {
    const categoryPath = category === "all"
      ? "/blog"
      : `/blog/category/${encodeURIComponent(category)}`;
    const canonicalPath = currentPage > 1
      ? `${categoryPath}/page/${currentPage}`
      : categoryPath;

    redirect(localizeHref(canonicalPath, locale));
  }
  const title = translateText(translations, "blog.title", locale, {
    ka: "პრაქტიკული გზამკვლევები და სიახლეები",
    en: "Practical guides and updates",
    ru: "Практические материалы и новости",
  });
  const allLabel = translateText(translations, "blog.filter.all", locale, null);
  const emptyLabel = translateText(translations, "blog.empty", locale, null);
  const previousLabel = translateText(translations, "pagination.previous", locale, {
    ka: "წინა",
    en: "Previous",
    ru: "Назад",
  });
  const nextLabel = translateText(translations, "pagination.next", locale, {
    ka: "შემდეგი",
    en: "Next",
    ru: "Далее",
  });

  const [categoriesResponse, postsResponse] = await Promise.all([
    getCategories({ locale }),
    getBlog({ page: currentPage, category, locale }),
  ]);
  const categoriesData = Array.isArray(categoriesResponse?.data)
    ? categoriesResponse.data
    : Array.isArray(categoriesResponse)
      ? categoriesResponse
      : [];
  const posts = Array.isArray(postsResponse?.data) ? postsResponse.data : [];
  const lastPage = Math.max(1, Number(postsResponse?.meta?.last_page || 1));

  const categories = [
    ...(allLabel ? [{ name: allLabel, slug: "all" }] : []),
    ...categoriesData.filter((item) => item?.name && item?.slug),
  ];

  return (
    <div className="bg-background px-4 pb-20 pt-28 sm:pt-32">
      {showPageSchema ? (
        <CmsPageSchema pageKey="blog" fallback={<BlogSchema />} />
      ) : null}
      <div className="mx-auto max-w-6xl">
        {heading || title ? (
          <h1 className="text-center text-3xl font-bold text-on-surface">
            {heading || title}
          </h1>
        ) : null}

        {categories.length ? (
          <div className="mt-6 flex flex-wrap justify-center gap-3">
            {categories.map((cat) => {
              const isActive = category === cat.slug;

              return (
                <Link
                  key={cat.slug}
                  href={localizeHref(
                    cat.slug === "all" ? "/blog" : `/blog/category/${cat.slug}`,
                    locale,
                  )}
                  className={`inline-flex min-h-11 items-center rounded-xl border px-4 py-2 text-sm transition-all duration-200 ${
                    isActive
                      ? "border-primary-container bg-primary-container text-on-primary-container shadow-lg shadow-blue-500/20"
                      : "border-outline-variant/30 bg-surface-container-low text-on-surface-variant hover:border-secondary/40 hover:text-on-surface"
                  }`}
                >
                  {cat.name}
                </Link>
              );
            })}
          </div>
        ) : null}

        <BlogList
          category={category}
          currentPage={currentPage}
          emptyLabel={emptyLabel}
          lastPage={lastPage}
          locale={locale}
          nextLabel={nextLabel}
          posts={posts}
          previousLabel={previousLabel}
        />
      </div>
    </div>
  );
}
