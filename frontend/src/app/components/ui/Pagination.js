import Link from "next/link";

function getPageHref({ basePath, category, page }) {
  const normalizedPage = Math.max(1, Number(page) || 1);
  const categoryPath =
    category && category !== "all" ? `${basePath}/category/${category}` : basePath;

  return normalizedPage === 1
    ? categoryPath
    : `${categoryPath}/page/${normalizedPage}`;
}

export default function Pagination({
  basePath,
  category = "all",
  currentPage = 1,
  lastPage = 1,
}) {
  if (lastPage <= 1) return null;

  const start = Math.max(1, currentPage - 2);
  const end = Math.min(lastPage, currentPage + 2);
  const pages = [];

  for (let i = start; i <= end; i++) pages.push(i);

  return (
    <nav
      className="flex justify-center mt-16 items-center gap-2"
      aria-label="Pagination"
    >
      <Link
        href={getPageHref({ basePath, category, page: currentPage - 1 })}
        aria-disabled={currentPage === 1}
        className={`w-10 h-10 flex items-center justify-center rounded-full border text-sm font-medium transition ${
          currentPage === 1
            ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
        }`}
      >
        ←
      </Link>

      {start > 1 && (
        <>
          <Link
            href={getPageHref({ basePath, category, page: 1 })}
            className="w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
          >
            1
          </Link>
          <span className="text-gray-400 px-1">...</span>
        </>
      )}

      {pages.map((page) => (
        <Link
          key={page}
          href={getPageHref({ basePath, category, page })}
          aria-current={page === currentPage ? "page" : undefined}
          className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border ${
            page === currentPage
              ? "bg-[#0B3C5D] text-white border-[#0B3C5D] shadow-md"
              : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
          }`}
        >
          {page}
        </Link>
      ))}

      {end < lastPage && (
        <>
          <span className="text-gray-400 px-1">...</span>
          <Link
            href={getPageHref({ basePath, category, page: lastPage })}
            className="w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
          >
            {lastPage}
          </Link>
        </>
      )}

      <Link
        href={getPageHref({ basePath, category, page: currentPage + 1 })}
        aria-disabled={currentPage === lastPage}
        className={`w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition border ${
          currentPage === lastPage
            ? "opacity-40 pointer-events-none bg-gray-100 text-gray-400 border-gray-200"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
        }`}
      >
        →
      </Link>
    </nav>
  );
}
