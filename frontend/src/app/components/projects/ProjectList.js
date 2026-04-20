import Link from "next/link";
import Image from "next/image";
import CategoryFilters from "@/app/components/ui/CategoryFilters";
import Pagination from "@/app/components/ui/Pagination";
import { mediaUrl } from "@/lib/media";

export default function ProjectList({
  projects = [],
  categories = [],
  meta = {},
  page = 1,
  category = "all",
}) {
  const currentPage = meta?.current_page || page || 1;
  const lastPage = meta?.last_page || 1;

  return (
    <div className="mt-10">
      <CategoryFilters
        basePath="/projects"
        categories={categories}
        currentCategory={category}
      />

      <div className="grid md:grid-cols-3 gap-6">
        {projects.map((project) => (
          <Link
            key={project.slug}
            href={`/projects/${project.slug}`}
            className="group block bg-white rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden"
          >
            <div className="relative overflow-hidden">
              <Image
                src={mediaUrl(project.image)}
                alt={project.title}
                width={400}
                height={300}
                sizes="(max-width: 768px) 100vw, 33vw"
                className="h-48 w-full object-cover"
              />

              {project.category?.name && (
                <span className="absolute top-3 left-3 bg-[#00C2A8] text-white text-xs px-3 py-1 rounded-full shadow">
                  {project.category.name}
                </span>
              )}
            </div>

            <div className="p-5">
              <h3 className="font-semibold text-[#0B3C5D] text-lg line-clamp-2 group-hover:text-[#00C2A8] transition">
                {project.title}
              </h3>

              <p className="mt-2 text-sm text-gray-600 line-clamp-3">
                {project.excerpt}
              </p>

              <div className="mt-4 text-sm font-medium text-[#00C2A8] opacity-0 group-hover:opacity-100 transition">
                ნახე დეტალურად →
              </div>
            </div>
          </Link>
        ))}
      </div>

      <Pagination
        basePath="/projects"
        category={category}
        currentPage={currentPage}
        lastPage={lastPage}
      />
    </div>
  );
}
