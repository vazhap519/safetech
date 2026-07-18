import Link from "next/link";

import BlogList from "@/app/components/blog/BlogList";
import { getCategories } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function BlogPage({ searchParams }) {
  const params = await searchParams;
  const category = params?.category || "all";
  const [locale, { translations }] = await Promise.all([
    getCurrentLocale(),
    getSiteSettings(),
  ]);
  const title = translateText(translations, "blog.title", locale, null);
  const allLabel = translateText(translations, "blog.filter.all", locale, null);

  let categoriesData = [];

  try {
    const res = await getCategories({ locale });
    categoriesData = res?.data || res || [];
  } catch (e) {
    console.error("Categories fetch failed:", e);
  }

  const categories = [
    ...(allLabel ? [{ name: allLabel, slug: "all" }] : []),
    ...categoriesData.filter((item) => item?.name && item?.slug),
  ];

  return (
    <main className="bg-background px-4 pb-20 pt-28 sm:pt-32">
      <div className="mx-auto max-w-6xl">
        {title ? (
          <h1 className="text-center text-3xl font-bold text-on-surface">
            {title}
          </h1>
        ) : null}

        {categories.length ? (
          <div className="mt-6 flex flex-wrap justify-center gap-3">
            {categories.map((cat) => {
              const isActive = category === cat.slug;

              return (
                <Link
                  key={cat.slug}
                  href={`/blog?category=${cat.slug}`}
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

        <BlogList category={category} locale={locale} />
      </div>
    </main>
  );
}
