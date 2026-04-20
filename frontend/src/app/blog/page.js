import BlogList from "@/app/components/blog/BlogList";
import { buildMetadata } from "@/lib/seo";
import { getBaseUrl } from "@/lib/config";
import { getBlog, getCategories, getEmpty, getSeoByKey } from "@/lib/datafetch";
import EmptyState from "../components/ui/EmptyState";

/* =========================
   SEO (BLOG 🔥)
========================= */
export async function generateMetadata({ searchParams }) {
  const params = await searchParams;
  const seo = await getSeoByKey("blog");
  const data = seo?.data;

  const BASE_URL = getBaseUrl();
  const page = Number(params?.page || 1);
  const category = params?.category;

  let url = `${BASE_URL}/blog`;

  if (category && category !== "all") {
    url = `${BASE_URL}/blog/category/${category}`;
  }

  if (page > 1) {
    url += `/page/${page}`;
  }

  /* 🔥 NOINDEX */
  let noindex = false;

  if (page > 5) {
    noindex = true;
  }

  return {
    ...buildMetadata({
      title: data?.title,
      description: data?.description,
      image: data?.og?.image,
      keywords: data?.keywords,
      og: data?.og,
      path: data?.slug || "/blog",
    }),

    alternates: {
      canonical: url,
    },

    robots: {
      index: !noindex,
      follow: true,
    },
  };
}

/* =========================
   PAGE
========================= */




export default async function BlogPage({ searchParams }) {
  const params = await searchParams;
  const page = Number(params?.page || 1);
  const category = params?.category || "all";

  const [res, categoriesRes, emptyRes] = await Promise.all([
    getBlog({ page, category }),
    getCategories().catch(() => null),
    getEmpty().catch(() => null),
  ]);

  const meta = res?.meta ?? {};
  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data || [];
  const empty = emptyRes?.data || emptyRes || null;

  /* ❌ backend down */
  if (!res || res.error) {
    return <EmptyState empty={empty} />;
  }

  const posts = res?.data ?? []; // ✅ სწორია

  /* 📭 empty data */
  if (posts.length === 0) {
    return <EmptyState empty={empty} />;
  }

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">

        <h1 className="text-3xl font-bold text-[#0B3C5D] text-center mb-10">
          ბლოგი
        </h1>

        <BlogList
          posts={posts}
          categories={categories}
          meta={meta}
          page={page}
          category={category}
        />

      </div>
    </main>
  );
}
