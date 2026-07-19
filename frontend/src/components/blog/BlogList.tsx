import Image from "next/image";
import Link from "next/link";

import Pagination from "@/components/ui/Pagination";
import type { Locale } from "@/lib/locales";
import { localizeHref } from "@/lib/seo";

const DEFAULT_IMAGE = "/brand-preview.svg";

type BlogPostCard = {
    title?: string;
    slug?: string;
    excerpt?: string;
    image?: string;
};

type BlogListProps = {
    posts: BlogPostCard[];
    category?: string;
    currentPage: number;
    emptyLabel?: string;
    lastPage: number;
    locale: Locale;
    nextLabel?: string;
    previousLabel?: string;
};

export default function BlogList({
    posts,
    category = "all",
    currentPage,
    emptyLabel,
    lastPage,
    locale,
    nextLabel,
    previousLabel,
}: BlogListProps) {
    return (
        <>
            <div className="mt-10 grid gap-6 md:grid-cols-3">
                {posts
                    .filter((post) => post.slug)
                    .map((post) => (
                        <Link
                            className="block h-full"
                            key={post.slug}
                            href={localizeHref(`/blog/${post.slug}`, locale)}
                        >
                            <article className="h-full overflow-hidden rounded-lg border border-outline-variant/20 bg-surface-container-low p-4 shadow-card transition hover:-translate-y-1 hover:border-secondary/30 hover:bg-surface-container">
                                <div className="relative aspect-[16/9] w-full overflow-hidden rounded-lg border border-outline-variant/20">
                                    <Image
                                        src={post.image || DEFAULT_IMAGE}
                                        alt={post.title || ""}
                                        fill
                                        sizes="(max-width: 768px) 100vw, 33vw"
                                        className="object-cover"
                                    />
                                </div>

                                {post.title ? (
                                    <h2 className="mt-4 line-clamp-2 font-semibold text-on-surface">
                                        {post.title}
                                    </h2>
                                ) : null}

                                {post.excerpt ? (
                                    <p className="mt-2 line-clamp-3 text-sm leading-relaxed text-on-surface-variant">
                                        {post.excerpt}
                                    </p>
                                ) : null}
                            </article>
                        </Link>
                    ))}
            </div>

            {posts.length === 0 && emptyLabel ? (
                <p className="mt-10 text-center text-on-surface-variant">
                    {emptyLabel}
                </p>
            ) : null}

            <Pagination
                basePath="/blog"
                category={category}
                currentPage={currentPage}
                lastPage={lastPage}
                locale={locale}
                nextLabel={nextLabel}
                previousLabel={previousLabel}
            />
        </>
    );
}
