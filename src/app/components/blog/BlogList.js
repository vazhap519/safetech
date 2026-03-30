

import Link from "next/link";
import { getBlog, getCategories } from "@/lib/datafetch";

export default async function BlogList({ page = 1, category = "all" }) {
  const [blogRes, catRes] = await Promise.all([
    getBlog({ page, category }),
    getCategories(),
  ]);
console.log("CATEGORIES:", catRes);
  if (!blogRes || blogRes.error) {
    return <p className="text-center mt-10 text-red-500">ვერ ჩაიტვირთა</p>;
  }

  const posts = blogRes?.data || [];
  const meta = blogRes?.meta || {};

  // const categories = [
  //   { name: "ყველა", slug: "all" },
  //   ...(catRes?.data || []),
  // ];
const categories = [
  { name: "ყველა", slug: "all" },
  ...(catRes || []),
];
  const currentPage = meta?.current_page || 1;
  const lastPage = meta?.last_page || 1;

  return (
    <div className="mt-10">

      {/* =========================
         🔥 CATEGORY FILTER (DYNAMIC)
      ========================= */}
  <div className="flex gap-3 overflow-x-auto md:overflow-visible md:flex-wrap md:justify-center pb-2 mb-12 no-scrollbar">

  {categories.map((cat) => (
    <Link
      key={cat.slug}
      href={`/blog?category=${cat.slug}`}
      className={`flex-shrink-0 px-5 py-2 rounded-full text-sm font-medium transition-all duration-300 border
        ${
          category === cat.slug
            ? "bg-[#00C2A8] text-white border-[#00C2A8] shadow-md"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:border-[#00C2A8] hover:text-[#00C2A8]"
        }`}
    >
      {cat.name}
    </Link>
  ))}

</div>

      {/* =========================
         GRID
      ========================= */}
      <div className="grid md:grid-cols-3 gap-6">

        {posts.map((post) => (
          <Link
            key={post.slug}
            href={`/blog/${post.slug}`}
            className="block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden"
          >
            <img
              src={post.image}
              alt={post.title}
              className="h-44 w-full object-cover"
            />

            <div className="p-4">

              {/* CATEGORY */}
              <span className="text-xs text-[#00C2A8] font-semibold uppercase">
                {post.category?.name}
              </span>

              {/* TITLE */}
              <h3 className="mt-1 font-semibold text-[#0B3C5D] line-clamp-2">
                {post.title}
              </h3>

              {/* META */}
              <div className="mt-2 text-xs text-gray-500 flex gap-2 flex-wrap">
                {post.author?.name && <span>✍️ {post.author.name}</span>}
                {post.created_at && (
                  <span>
                    📅 {new Date(post.created_at).toLocaleDateString("ka-GE")}
                  </span>
                )}
                {post.reading_time && <span>⏱ {post.reading_time} წთ</span>}
              </div>

              {/* EXCERPT */}
              <p className="mt-2 text-sm text-gray-600 line-clamp-3">
                {post.excerpt}
              </p>

            </div>
          </Link>
        ))}

      </div>

      {/* =========================
         🔥 CLEAN PAGINATION
      ========================= */}
{lastPage > 1 && (
  <div className="flex justify-center mt-16 items-center gap-2">

    {/* PREV */}
    <Link
      href={`/blog?page=${currentPage - 1}${category ? `&category=${category}` : ""}`}
      className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition-all duration-300 border
        ${
          currentPage === 1
            ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
        }`}
    >
      ←
    </Link>

    {/* NUMBERS */}
    <div className="flex gap-2 overflow-x-auto md:overflow-visible no-scrollbar">

      {Array.from({ length: lastPage }).map((_, i) => {
        const p = i + 1;

        return (
          <Link
            key={p}
            href={`/blog?page=${p}${category ? `&category=${category}` : ""}`}
            className={`flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition-all duration-300 border
              ${
                p === currentPage
                  ? "bg-[#0B3C5D] text-white border-[#0B3C5D] shadow-md"
                  : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
              }`}
          >
            {p}
          </Link>
        );
      })}

    </div>

    {/* NEXT */}
    <Link
      href={`/blog?page=${currentPage + 1}${category ? `&category=${category}` : ""}`}
      className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition-all duration-300 border
        ${
          currentPage === lastPage
            ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
        }`}
    >
      →
    </Link>

  </div>
)}

    </div>
  );
}