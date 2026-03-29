import BlogList from "@/app/components/blog/BlogList";

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
import { buildMetadata } from "@/lib/seo";
import { getSeoByKey } from "@/lib/datafetch";


/* =========================
   SEO (BLOG 🔥)
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("blog");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/blog",
  });
}

/* =========================
   PAGE
========================= */


export default async function BlogPage({ searchParams }) {
  const params = await searchParams;

  const page = Number(params?.page || 1);
  const category = params?.category || "all";

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">
        <h1 className="text-3xl font-bold text-[#0B3C5D] text-center">
          ბლოგი
        </h1>

        <BlogList page={page} category={category} />
      </div>
    </main>
  );
}