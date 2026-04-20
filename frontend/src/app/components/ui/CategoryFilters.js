import Link from "next/link";

export default function CategoryFilters({
  basePath,
  categories = [],
  currentCategory = "all",
}) {
  const items = [{ name: "ყველა", slug: "all" }, ...categories];

  return (
    <div className="flex gap-3 overflow-x-auto md:flex-wrap md:justify-center mb-12 pb-2">
      {items.map((cat) => {
        const active =
          currentCategory === cat.slug ||
          (!currentCategory && cat.slug === "all");
        const href =
          cat.slug === "all" ? basePath : `${basePath}/category/${cat.slug}`;

        return (
          <Link
            key={cat.slug}
            href={href}
            className={`px-5 py-2 rounded-lg text-sm font-medium border transition whitespace-nowrap ${
              active
                ? "bg-[#00C2A8] text-white border-[#00C2A8]"
                : "bg-white text-[#0B3C5D] border-gray-200 hover:border-[#00C2A8]"
            }`}
          >
            {cat.name}
          </Link>
        );
      })}
    </div>
  );
}
