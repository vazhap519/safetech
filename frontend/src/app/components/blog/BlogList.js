import Link from "next/link";
import Image from "next/image";
import CategoryFilters from "@/app/components/ui/CategoryFilters";
import Pagination from "@/app/components/ui/Pagination";
import { mediaUrl } from "@/lib/media";

export default function BlogList({
  posts = [],
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
        basePath="/blog"
        categories={categories}
        currentCategory={category}
      />

      <div className="grid md:grid-cols-3 gap-6">
        {posts.map((post) => (
          <Link
            key={post.slug}
            href={`/blog/${post.slug}`}
            className="block bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
          >
            <Image
              src={mediaUrl(post.image)}
              alt={post.title}
              width={400}
              height={200}
              sizes="(max-width: 768px) 100vw, 33vw"
              className="h-44 w-full object-cover"
            />

            <div className="p-4">
              {post.category?.name && (
                <span className="text-xs text-[#00C2A8] font-semibold uppercase">
                  {post.category.name}
                </span>
              )}

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

      <Pagination
        basePath="/blog"
        category={category}
        currentPage={currentPage}
        lastPage={lastPage}
      />
    </div>
  );
}
