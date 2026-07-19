import Link from "next/link";

import type { Locale } from "@/lib/locales";
import { localizeHref } from "@/lib/seo";

function getPageHref({
    basePath,
    category,
    locale,
    page,
}: {
    basePath: string;
    category: string;
    locale: Locale;
    page: number;
}) {
    const normalizedPage = Math.max(1, Number(page) || 1);
    const categoryPath =
        category && category !== "all"
            ? `${basePath}/category/${category}`
            : basePath;
    const path =
        normalizedPage === 1
            ? categoryPath
            : `${categoryPath}/page/${normalizedPage}`;

    return localizeHref(path, locale);
}

type PaginationProps = {
    basePath: string;
    category?: string;
    currentPage?: number;
    lastPage?: number;
    locale: Locale;
    nextLabel?: string;
    previousLabel?: string;
};

export default function Pagination({
    basePath,
    category = "all",
    currentPage = 1,
    lastPage = 1,
    locale,
    nextLabel = "Next",
    previousLabel = "Previous",
}: PaginationProps) {
    if (lastPage <= 1) return null;

    const start = Math.max(1, currentPage - 2);
    const end = Math.min(lastPage, currentPage + 2);
    const pages = Array.from(
        { length: end - start + 1 },
        (_, index) => start + index,
    );

    return (
        <nav
            className="mt-14 flex flex-wrap items-center justify-center gap-2"
            aria-label="Pagination"
        >
            <Link
                href={getPageHref({
                    basePath,
                    category,
                    locale,
                    page: currentPage - 1,
                })}
                aria-disabled={currentPage === 1}
                aria-label={previousLabel}
                rel="prev"
                className={`flex size-11 items-center justify-center rounded-lg border text-sm font-medium transition ${
                    currentPage === 1
                        ? "pointer-events-none border-outline-variant/20 bg-surface-container opacity-40"
                        : "border-outline-variant/30 bg-surface-container-low text-on-surface hover:border-secondary hover:text-secondary"
                }`}
            >
                <span aria-hidden="true">&larr;</span>
            </Link>

            {start > 1 ? (
                <>
                    <Link
                        href={getPageHref({
                            basePath,
                            category,
                            locale,
                            page: 1,
                        })}
                        className="flex size-11 items-center justify-center rounded-lg border border-outline-variant/30 bg-surface-container-low text-sm font-medium text-on-surface hover:border-secondary hover:text-secondary"
                    >
                        1
                    </Link>
                    <span className="px-1 text-on-surface-variant" aria-hidden="true">
                        ...
                    </span>
                </>
            ) : null}

            {pages.map((page) => (
                <Link
                    key={page}
                    href={getPageHref({ basePath, category, locale, page })}
                    aria-current={page === currentPage ? "page" : undefined}
                    className={`flex size-11 items-center justify-center rounded-lg border text-sm font-medium transition ${
                        page === currentPage
                            ? "border-primary-container bg-primary-container text-on-primary-container"
                            : "border-outline-variant/30 bg-surface-container-low text-on-surface hover:border-secondary hover:text-secondary"
                    }`}
                >
                    {page}
                </Link>
            ))}

            {end < lastPage ? (
                <>
                    <span className="px-1 text-on-surface-variant" aria-hidden="true">
                        ...
                    </span>
                    <Link
                        href={getPageHref({
                            basePath,
                            category,
                            locale,
                            page: lastPage,
                        })}
                        className="flex size-11 items-center justify-center rounded-lg border border-outline-variant/30 bg-surface-container-low text-sm font-medium text-on-surface hover:border-secondary hover:text-secondary"
                    >
                        {lastPage}
                    </Link>
                </>
            ) : null}

            <Link
                href={getPageHref({
                    basePath,
                    category,
                    locale,
                    page: currentPage + 1,
                })}
                aria-disabled={currentPage === lastPage}
                aria-label={nextLabel}
                rel="next"
                className={`flex size-11 items-center justify-center rounded-lg border text-sm font-medium transition ${
                    currentPage === lastPage
                        ? "pointer-events-none border-outline-variant/20 bg-surface-container opacity-40"
                        : "border-outline-variant/30 bg-surface-container-low text-on-surface hover:border-secondary hover:text-secondary"
                }`}
            >
                <span aria-hidden="true">&rarr;</span>
            </Link>
        </nav>
    );
}
