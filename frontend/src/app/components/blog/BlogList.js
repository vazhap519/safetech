"use client";

import { useState, useEffect, useRef } from "react";
import Link from "next/link";
import { getBlog } from "@/lib/datafetch";

const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

export default function BlogList({ category }) {
  const [posts, setPosts] = useState([]);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loading, setLoading] = useState(false);

  const loaderRef = useRef(null);

  /* =========================
     FETCH DATA
  ========================= */
  const loadPosts = async (pageNum = 1, reset = false) => {
    if (loading) return;

    setLoading(true);

    try {
      const res = await getBlog({
        page: pageNum,
        category,
      });

      const newPosts = res?.data || [];

      setPosts((prev) =>
        reset ? newPosts : [...prev, ...newPosts]
      );

      // ✅ meta-based pagination (BEST)
      if (!res?.meta || pageNum >= res.meta.last_page) {
        setHasMore(false);
      }

    } catch (err) {
      console.error("Blog load error:", err);
    } finally {
      setLoading(false);
    }
  };

  /* =========================
     CATEGORY CHANGE
  ========================= */
  useEffect(() => {
    setPosts([]);
    setPage(1);
    setHasMore(true);

    loadPosts(1, true);
  }, [category]);

  /* =========================
     PAGE CHANGE
  ========================= */
  useEffect(() => {
    if (page === 1) return;
    loadPosts(page);
  }, [page]);

  /* =========================
     INFINITE SCROLL (FIXED)
  ========================= */
  useEffect(() => {
    if (!loaderRef.current) return;

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting && hasMore && !loading) {
          setPage((prev) => prev + 1);
        }
      },
      { threshold: 0.5 }
    );

    observer.observe(loaderRef.current);

    return () => observer.disconnect();
  }, [hasMore, loading]);

  /* =========================
     UI
  ========================= */
  return (
    <>
      {/* POSTS */}
      <div className="grid md:grid-cols-3 gap-6 mt-10">
        {posts.map((post) => (
          <Link key={post.slug} href={`/blog/${post.slug}`}>
            <div className="bg-white rounded-xl shadow p-4 hover:shadow-lg transition cursor-pointer">

              <img
                src={post.image || DEFAULT_IMAGE}
                alt={post.title || "Blog image"}
                className="rounded-lg h-40 w-full object-cover"
              />

              <h2 className="mt-4 font-semibold text-[#0B3C5D] line-clamp-2">
                {post.title}
              </h2>

              <p className="text-sm text-gray-600 mt-2 line-clamp-3">
                {post.excerpt}
              </p>

            </div>
          </Link>
        ))}
      </div>

      {/* EMPTY */}
      {!loading && posts.length === 0 && (
        <div className="text-center mt-10 text-gray-400">
          პოსტები ვერ მოიძებნა 😔
        </div>
      )}

      {/* LOADER */}
      {hasMore && (
        <div
          ref={loaderRef}
          className="h-20 flex items-center justify-center"
        >
          <span className="text-gray-400 text-sm">
            {loading ? "იტვირთება..." : "გადაასკროლე ქვემოთ"}
          </span>
        </div>
      )}
    </>
  );
}