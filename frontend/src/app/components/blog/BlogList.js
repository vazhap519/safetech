

import Link from "next/link";
import { getBlog, getCategories } from "@/lib/datafetch";

export default async function BlogList({ page = 1, category = "all" }) {
  const [blogRes, catRes] = await Promise.all([
    getBlog({ page, category }),
    getCategories(),
  ]);

  if (!blogRes || blogRes.error) {
    return <p className="text-center mt-10 text-red-500">ვერ ჩაიტვირთა</p>;
  }

  const posts = blogRes?.data || [];
  const meta = blogRes?.meta || {};

  const categories = [
    { name: "ყველა", slug: "all" },
    ...(catRes?.data || []),
  ];

  const currentPage = meta?.current_page || 1;
  const lastPage = meta?.last_page || 1;

  return (
    <div className="mt-10">

      {/* =========================
         🔥 CATEGORY FILTER (DYNAMIC)
      ========================= */}
      <div className="flex flex-wrap justify-center gap-3 mb-10">

        {categories.map((cat) => (
          <Link
            key={cat.slug}
            href={`/blog?category=${cat.slug}`}
            className={`px-4 py-2 rounded-full text-sm font-medium transition
              ${
                category === cat.slug
                  ? "bg-[#00C2A8] text-white shadow"
                  : "bg-gray-100 border border-gray-400 text-gray-800 hover:bg-gray-200"
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
        <div className="flex justify-center mt-16">

          <div className="flex items-center gap-2">

            {/* PREV */}
            <Link
              href={`/blog?page=${currentPage - 1}&category=${category}`}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition
                ${
                  currentPage === 1
      ? "opacity-40 pointer-events-none bg-gray-200 text-gray-500"
      : "bg-gray-100 border border-gray-400 text-gray-800 hover:bg-gray-200"
                }`}
            >
              ←
            </Link>

            {/* NUMBERS */}
            {Array.from({ length: lastPage }).map((_, i) => {
              const p = i + 1;

              return (
                <Link
                  key={p}
                  href={`/blog?page=${p}&category=${category}`}
                  className={`px-4 py-2 rounded-lg text-sm font-medium transition
                    ${
                      p === currentPage
                         ? "bg-[#00C2A8] text-white border-[#00C2A8] shadow-md"
      : "bg-gray-100 border border-gray-400 text-gray-800 hover:bg-gray-200"
                    }`}
                >
                  {p}
                </Link>
              );
            })}

            {/* NEXT */}
            <Link
              href={`/blog?page=${currentPage + 1}&category=${category}`}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition
                ${
                  currentPage === lastPage
                              ? "bg-[#00C2A8] text-white border-[#00C2A8] shadow-md"
      : "bg-gray-100 border border-gray-400 text-gray-800 hover:bg-gray-200"
                }`}
            >
              →
            </Link>

          </div>

        </div>
      )}

    </div>
  );
}