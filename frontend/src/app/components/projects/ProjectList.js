import Link from "next/link";
import { getProjects } from "@/lib/datafetch";
import { getProjectCategories } from "@/lib/datafetch";
import Image from "next/image";

export default async function ProjectList({ page = 1, category = "all" }) {
const [projRes, catRes] = await Promise.all([
  getProjects({ page, category }),
  getProjectCategories(),
]);

  if (!projRes || projRes.error) {
    return <p className="text-center mt-10 text-red-500">ვერ ჩაიტვირთა</p>;
  }

  const projects = projRes?.data || [];
  const meta = projRes?.meta || {};

const categoriesData = Array.isArray(catRes)
  ? catRes
  : catRes?.data || [];

const categories = [
  { name: "ყველა", slug: "all" },
  ...categoriesData,
];

  const currentPage = meta?.current_page || 1;
  const lastPage = meta?.last_page || 1;

  return (
    <div className="mt-10">

      {/* =========================
         🔥 CATEGORY FILTER
      ========================= */}
      <div className="flex gap-3 overflow-x-auto md:flex-wrap md:justify-center mb-12">

        {categories.map((cat) => (
          <Link
            key={cat.slug}
            href={`/projects?category=${cat.slug}`}
            className={`px-5 py-2 rounded-full text-sm font-medium border transition
              ${
                category === cat.slug
                  ? "bg-[#00C2A8] text-white border-[#00C2A8]"
                  : "bg-white text-[#0B3C5D] border-gray-200 hover:border-[#00C2A8]"
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

  {projects.map((project) => (
    <Link
      key={project.slug}
      href={`/projects/${project.slug}`}
      className="group block bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden"
    >

      {/* IMAGE */}
      <div className="relative overflow-hidden">
   <Image
  src={project.image}
  alt={project.title}
  width={400}
  height={300}
  className="h-48 w-full object-cover"
  priority={true} // 🔥 LCP FIX
  fetchPriority="high"
/>

        {/* CATEGORY BADGE */}
        {project.category?.name && (
          <span className="absolute top-3 left-3 bg-[#00C2A8] text-white text-xs px-3 py-1 rounded-full shadow">
            {project.category.name}
          </span>
        )}
      </div>

      {/* CONTENT */}
      <div className="p-5">

        {/* TITLE */}
        <h3 className="font-semibold text-[#0B3C5D] text-lg line-clamp-2 group-hover:text-[#00C2A8] transition">
          {project.title}
        </h3>

        {/* EXCERPT */}
        <p className="mt-2 text-sm text-gray-600 line-clamp-3">
          {project.excerpt}
        </p>

        {/* CTA */}
        <div className="mt-4 text-sm font-medium text-[#00C2A8] opacity-0 group-hover:opacity-100 transition">
          ნახე დეტალურად →
        </div>

      </div>
    </Link>
  ))}

</div>

      {/* =========================
         PAGINATION
      ========================= */}
    {lastPage > 1 && (
  <div className="flex justify-center mt-16 items-center gap-2">

    {/* PREV */}
    <Link
      href={`/projects?page=${currentPage - 1}&category=${category}`}
      className={`w-10 h-10 flex items-center justify-center rounded-full border text-sm font-medium transition
        ${currentPage === 1
          ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
          : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
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
          href={`/projects?page=${p}&category=${category}`}
          className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border
            ${p === currentPage
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
      href={`/projects?page=${currentPage + 1}&category=${category}`}
      className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border
        ${currentPage === lastPage
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