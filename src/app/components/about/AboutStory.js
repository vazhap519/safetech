"use client";

import { useEffect, useState } from "react";
import useFadeIn from "@/app/hooks/useFadeIn";

export default function AboutStory({ Story }) {
  const [ref, visible] = useFadeIn();

  // ? FIXED COUNTER (SAFE + NO CRASH)
  function Counter({ value, isVisible, duration = 1500 }) {
    const [count, setCount] = useState(0);

    useEffect(() => {
      if (!isVisible) return;

      const end = Number(value) || 0;

      if (end === 0) {
        setCount(0);
        return;
      }

      const stepTime = 16;
      const totalSteps = duration / stepTime;
      const increment = end / totalSteps;

      let current = 0;

      const timer = setInterval(() => {
        current += increment;

        if (current >= end) {
          setCount(end);
          clearInterval(timer);
        } else {
          setCount(Math.floor(current));
        }
      }, stepTime);

      return () => clearInterval(timer);
    }, [isVisible, value, duration]);

    return <span>{count}</span>;
  }

  return (
    <section
      ref={ref}
      className={`
        py-24 
        bg-gray-50 
        transition-all duration-700
        ${visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"}
      `}
    >
      <div className="max-w-6xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            {Story?.title}
          </h2>

          <p className="mt-4 text-gray-600 max-w-2xl mx-auto">
            {Story?.description}
          </p>
        </div>

        {/* CONTENT */}
        <div className="grid md:grid-cols-2 gap-16 items-center">

          {/* TEXT */}
          <div
            className="
              text-gray-700 
              text-lg 
              leading-relaxed 
              space-y-6
              max-w-xl
              [&>p]:mb-4
            "
            dangerouslySetInnerHTML={{
              __html: Story?.content || "",
            }}
          />

          {/* STATS */}
          <div className="grid grid-cols-2 gap-6">

            {Array.isArray(Story?.stats) &&
              Story.stats.map((item, i) => {

                // ? SAFE RAW VALUE
                const raw = item?.story_stats_numbers ?? "";

                // ? SAFE SPLIT (NUMBER + SUFFIX)
                const match = String(raw).match(/^(\d+)(.*)$/);

                const number = match?.[1] ?? "0";
                const suffix = match?.[2] ?? "";

                return (
                  <div
                    key={i}
                    className={`
                      group
                      bg-white
                      border border-gray-200
                      rounded-2xl
                      p-6
                      text-center
                      transition-all duration-300
                      hover:-translate-y-2
                      hover:shadow-xl
                      ${
                        visible
                          ? "opacity-100 translate-y-0"
                          : "opacity-0 translate-y-10"
                      }
                    `}
                    style={{ transitionDelay: `${i * 120}ms` }}
                  >

                    {/* NUMBER */}
                    <h3 className="text-3xl font-bold text-[#00C2A8]">
                      <Counter value={number} isVisible={visible} />
                      {suffix}
                    </h3>

                    {/* LABEL */}
                    <p className="text-sm text-gray-500 mt-2">
                      {item?.story_stats_label ?? ""}
                    </p>

                  </div>
                );
              })}

          </div>

        </div>

      </div>
    </section>
  );
}