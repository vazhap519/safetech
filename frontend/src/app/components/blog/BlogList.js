"use client";

import { useState, useEffect, useRef } from "react";
import Link from "next/link";
import { blogPosts } from "@/data/blog";

const POSTS_PER_LOAD = 6;

export default function BlogList({ category }) {
  const [visiblePosts, setVisiblePosts] = useState([]);
  const [page, setPage] = useState(1);
  const loaderRef = useRef(null);

  // 🔥 FILTER
  const filteredPosts =
    category === "all"
      ? blogPosts
      : blogPosts.filter((p) => p.category === category);

  // 🔥 INITIAL LOAD
  useEffect(() => {
    setVisiblePosts(filteredPosts.slice(0, POSTS_PER_LOAD));
    setPage(1);
  }, [category]);

  // 🔥 LOAD MORE
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting) {
          const nextPosts = filteredPosts.slice(
            page * POSTS_PER_LOAD,
            (page + 1) * POSTS_PER_LOAD
          );

          if (nextPosts.length > 0) {
            setVisiblePosts((prev) => [...prev, ...nextPosts]);
            setPage((prev) => prev + 1);
          }
        }
      },
      { threshold: 1 }
    );

    if (loaderRef.current) observer.observe(loaderRef.current);

    return () => observer.disconnect();
  }, [page, filteredPosts]);

  return (
    <>
      {/* POSTS */}
      <div className="grid md:grid-cols-3 gap-6 mt-10">
        {visiblePosts.map((post) => (
          <Link key={post.slug} href={`/blog/${post.slug}`}>
            <div className="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">

              <img
                src={post.image}
                alt={post.title}
                className="rounded-lg h-40 w-full object-cover"
              />

              <h2 className="mt-4 font-semibold text-[#0B3C5D]">
                {post.title}
              </h2>

              <p className="text-sm text-gray-600 mt-2">
                {post.desc}
              </p>

            </div>
          </Link>
        ))}
      </div>

      {/* LOADER */}
      <div ref={loaderRef} className="h-20 flex items-center justify-center">
        <span className="text-gray-400 text-sm">
          იტვირთება...
        </span>
      </div>
    </>
  );
}