import Link from "next/link";
import Image from "next/image";
import { getCategories } from "@/lib/datafetch";

export default async function BlogList({
  posts = [],
  meta = {},
  page = 1,
  category = "all",
}) {
  const catRes = await getCategories();

  // ? FIX: ????? ?????????
  const categories = [
    { name: "?????", slug: "all" },
    ...(catRes?.data || []),
  ];

  const currentPage = meta?.current_page || 1;
  const lastPage = meta?.last_page || 1;

  return (
    <div className="mt-10">

      {/* CATEGORY */}
      <div className="flex gap-3 justify-center mb-12 flex-wrap">
        {categories.map((cat) => (
      <Link
    key={cat.slug}
    href={
      cat.slug === "all"
        ? `/blog`
        : `/blog/category/${cat.slug}`
    }
    className={`px-5 py-2 rounded-full text-sm font-medium border transition
      ${
        (category === cat.slug || (!category && cat.slug === "all"))
          ? "bg-[#00C2A8] text-white border-[#00C2A8]"
          : "bg-white text-[#0B3C5D] border-gray-200 hover:border-[#00C2A8]"
      }`}
  >
    {cat.name}
  </Link>
        ))}
      </div>

      {/* GRID */}
      <div className="grid md:grid-cols-3 gap-6">
        {posts.map((post) => (
          <Link
            key={post.slug}
            href={`/blog/${post.slug}`}
            className="block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden"
          >
            {/* ? FIX: next/image */}
            <Image
              src={
                post.image?.startsWith("http")
                  ? post.image
                  : `${process.env.NEXT_PUBLIC_API_URL}${post.image}`
              }
              alt={post.title}
              width={400}
              height={200}
              className="h-44 w-full object-cover"
            />

            <div className="p-4">
              <span className="text-xs text-[#00C2A8] font-semibold uppercase">
                {post.category?.name}
              </span>

              <h3 className="mt-1 font-semibold text-[#0B3C5D] line-clamp-2">
                {post.title}
              </h3>

              <p className="mt-2 text-sm text-gray-600 line-clamp-3">
                {post.excerpt}
              </p>
            </div>
          </Link>
        ))}
      </div>

      {/* PAGINATION */}
      {lastPage > 1 && (
        <div className="flex justify-center mt-16 items-center gap-2">

          {/* PREV */}
          <Link
          href={
  currentPage > 1
    ? category && category !== "all"
      ? `/blog/category/${category}/page/${currentPage - 1}`
      : `/blog/page/${currentPage - 1}`
    : "#"
}
            className={`w-10 h-10 flex items-center justify-center rounded-full border text-sm font-medium transition
              ${
                currentPage === 1
                  ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
                  : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
              }`}
          >
            ?
          </Link>

          {/* NUMBERS */}
          {Array.from({ length: lastPage }).map((_, i) => {
            const p = i + 1;

            return (
              <Link
                key={p}
               href={
  category && category !== "all"
    ? `/blog/category/${category}/page/${p}`
    : `/blog/page/${p}`
}
                className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border
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

          {/* NEXT */}
          <Link
           href={
  currentPage < lastPage
    ? category && category !== "all"
      ? `/blog/category/${category}/page/${currentPage + 1}`
      : `/blog/page/${currentPage + 1}`
    : "#"
}
            className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border
              ${
                currentPage === lastPage
                  ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
                  : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
              }`}
          >
            ?
          </Link>

        </div>
      )}
    </div>
  );
}