import BlogList from "@/app/components/blog/BlogList";
import Link from "next/link";
import { getCategories } from "@/lib/datafetch";

export default async function BlogPage({ searchParams }) {
 
  const params = await searchParams; 

  const category = params?.category || "all";

  let categoriesData = [];

  try {
    const res = await getCategories();
    categoriesData = res?.data || res || [];
  } catch (e) {
    console.error("Categories fetch failed:", e);
  }

  const categories = [
    { name: "ყველა", slug: "all" },
    ...categoriesData,
  ];

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">

        <h1 className="text-3xl font-bold text-[#0B3C5D] text-center">
          ბლოგი
        </h1>

        {/* FILTER */}
        <div className="flex justify-center gap-3 mt-6 flex-wrap">
          {categories.map((cat) => {
            const isActive = category === cat.slug;

            return (
              <Link
                key={cat.slug}
                href={`/blog?category=${cat.slug}`}
                className={`px-4 py-2 rounded-lg border text-sm transition-all duration-200
                  ${
                    isActive
                      ? "bg-[#00C2A8] text-white border-[#00C2A8] shadow-md"
                      : "bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200"
                  }`}
              >
                {cat.name}
              </Link>
            );
          })}
        </div>

        <BlogList category={category} />

      </div>
    </main>
  );
}