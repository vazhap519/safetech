import BlogList from "@/app/components/blog/BlogList";
import Link from "next/link";
import { getCategories } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";

export default async function BlogPage({ searchParams }) {
 
  const params = await searchParams; 

  const category = params?.category || "all";
  const locale = await getCurrentLocale();
  const copy = {
    ka: { all: "ყველა", title: "ბლოგი" },
    en: { all: "All", title: "Blog" },
    ru: { all: "Все", title: "Блог" },
  }[locale];

  let categoriesData = [];

  try {
    const res = await getCategories({ locale });
    categoriesData = res?.data || res || [];
  } catch (e) {
    console.error("Categories fetch failed:", e);
  }

  const categories = [
    { name: copy.all, slug: "all" },
    ...categoriesData,
  ];

  return (
    <main className="bg-background px-4 pb-20 pt-28 sm:pt-32">
      <div className="mx-auto max-w-6xl">

        <h1 className="text-center text-3xl font-bold text-on-surface">
          {copy.title}
        </h1>

        {/* FILTER */}
        <div className="mt-6 flex flex-wrap justify-center gap-3">
          {categories.map((cat) => {
            const isActive = category === cat.slug;

            return (
              <Link
                key={cat.slug}
                href={`/blog?category=${cat.slug}`}
                className={`inline-flex min-h-11 items-center rounded-xl border px-4 py-2 text-sm transition-all duration-200
                  ${
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

        <BlogList category={category} locale={locale} />

      </div>
    </main>
  );
}
