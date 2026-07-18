"use client";
/* eslint-disable react-hooks/set-state-in-effect */

import Image from "next/image";
import Link from "next/link";
import { useCallback, useEffect, useRef, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { getBlog } from "@/lib/datafetch";

const DEFAULT_IMAGE = "/brand-preview.svg";

export default function BlogList({ category, locale }) {
  const { t } = useLocalization();
  const emptyLabel = t("blog.empty", null);
  const loadingLabel = t("blog.loading", null);
  const moreLabel = t("blog.more", null);
  const [posts, setPosts] = useState([]);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loading, setLoading] = useState(false);
  const loaderRef = useRef(null);
  const loadingRef = useRef(false);

  const loadPosts = useCallback(
    async (pageNum = 1, reset = false) => {
      if (loadingRef.current) return;

      loadingRef.current = true;
      setLoading(true);

      try {
        const res = await getBlog({
          page: pageNum,
          category,
          locale,
        });
        const newPosts = res?.data || [];

        setPosts((prev) => (reset ? newPosts : [...prev, ...newPosts]));
        setHasMore(Boolean(res?.meta && pageNum < res.meta.last_page));
      } catch (error) {
        if (process.env.NODE_ENV !== "production") {
          console.error("Blog load error:", error);
        }
      } finally {
        loadingRef.current = false;
        setLoading(false);
      }
    },
    [category, locale],
  );

  useEffect(() => {
    setPosts([]);
    setPage(1);
    setHasMore(true);
    void loadPosts(1, true);
  }, [category, loadPosts]);

  useEffect(() => {
    if (page === 1) return;
    void loadPosts(page);
  }, [loadPosts, page]);

  useEffect(() => {
    const loader = loaderRef.current;
    if (!loader) return;

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting && hasMore && !loadingRef.current) {
          setPage((prev) => prev + 1);
        }
      },
      { threshold: 0.5 },
    );

    observer.observe(loader);

    return () => observer.disconnect();
  }, [hasMore]);

  const loaderLabel = loading ? loadingLabel : moreLabel;

  return (
    <>
      <div className="mt-10 grid gap-6 md:grid-cols-3">
        {posts.map((post) => (
          <Link className="block h-full" key={post.slug} href={`/blog/${post.slug}`}>
            <article className="h-full cursor-pointer overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low p-4 shadow-card transition hover:-translate-y-1 hover:border-secondary/30 hover:bg-surface-container">
              <div className="relative h-40 w-full overflow-hidden rounded-xl border border-outline-variant/20">
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

      {!loading && posts.length === 0 && emptyLabel ? (
        <div className="mt-10 text-center text-on-surface-variant">{emptyLabel}</div>
      ) : null}

      {hasMore ? (
        <div ref={loaderRef} className="flex h-20 items-center justify-center">
          {loaderLabel ? (
            <span className="text-sm text-on-surface-variant">{loaderLabel}</span>
          ) : null}
        </div>
      ) : null}
    </>
  );
}
