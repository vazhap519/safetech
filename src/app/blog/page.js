import BlogList from "@/app/components/blog/BlogList";

export default async function BlogPage({ searchParams }) {
  const params = await searchParams;
  const category = params?.category || "all";

  const categories = ["all", "cctv", "network", "pos"];

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">

        <h1 className="text-3xl font-bold text-[#0B3C5D] text-center">
          ბლოგი
        </h1>

        {/* FILTER */}
        <div className="flex justify-center gap-3 mt-6 flex-wrap">
          {categories.map((cat) => (
            <a
              key={cat}
              href={`/blog?category=${cat}`}
              className={`px-4 py-2 rounded-lg border ${
                category === cat
                  ? "bg-[#00C2A8] text-white"
                  : "hover:bg-gray-100"
              }`}
            >
              {cat}
            </a>
          ))}
        </div>

        {/* 🔥 INFINITE LIST */}
        <BlogList category={category} />

      </div>
    </main>
  );
}