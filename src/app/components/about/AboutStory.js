"use client";

import { useEffect, useState } from "react";
import useFadeIn from "@/app/hooks/useFadeIn";

export default function AboutStory({ Story }) {
  const [ref, visible] = useFadeIn();

  // 🔥 CLEAN COUNTER (NO BUGS)
  function Counter({ value, isVisible, duration = 2000 }) {
    const [count, setCount] = useState(0);

    useEffect(() => {
      if (!isVisible) return;

      let start = 0;
      const end = parseInt(value?.toString().replace(/\D/g, ""));
      if (!end) return;

      const totalFrames = duration / 16;
      const increment = end / totalFrames;

      let frame = 0;
      let timer;

      function run() {
        timer = setInterval(() => {
          frame++;
          start += increment;

          if (frame >= totalFrames) {
            setCount(end);
            clearInterval(timer);

            // 🔁 loop restart clean
            setTimeout(() => {
              start = 0;
              frame = 0;
              setCount(0);
              run();
            }, 1200);

          } else {
            setCount(Math.floor(start));
          }
        }, 16);
      }

      run();

      return () => clearInterval(timer);
    }, [isVisible, value, duration]);

    return <span>{count}</span>;
  }

  return (
    <section
      ref={ref}
      className={`py-20 bg-[#F8FAFC] transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-6xl mx-auto px-4">

        {/* 🔥 TITLE */}
        <div className="text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-[#0B3C5D]">
            {Story?.title}
          </h2>

          <p className="mt-4 text-gray-600 max-w-2xl mx-auto">
            {Story?.description}
          </p>
        </div>

        {/* 🔥 CONTENT */}
        <div className="mt-12 grid md:grid-cols-2 gap-10 items-center">

          {/* TEXT (RichEditor HTML) */}
          <div
            className="space-y-4 text-gray-700 leading-relaxed
                       [&>p]:mb-3 [&>p]:leading-relaxed"
            dangerouslySetInnerHTML={{
              __html: Story?.content || "",
            }}
          />

          {/* 🔥 STATS */}
          <div className="grid grid-cols-2 gap-6">
            {Story?.stats?.map((item, i) => {
              const raw = item?.story_stats_numbers || "";

              // 👉 გამოვყოფთ რიცხვს და suffix-ს
              const number = raw.toString().replace(/\D/g, "");
              const suffix = raw.toString().replace(/[0-9]/g, "");

              return (
                <div
                  key={i}
                  className={`bg-white p-6 rounded-xl shadow text-center 
                  transition-all duration-500 hover:-translate-y-1 hover:shadow-lg
                  ${
                    visible
                      ? "opacity-100 translate-y-0"
                      : "opacity-0 translate-y-10"
                  }`}
                  style={{ transitionDelay: `${i * 120}ms` }}
                >
                  <h3 className="text-2xl font-bold text-[#00C2A8]">
                    <Counter value={number} isVisible={visible} />
                    {suffix}
                  </h3>

                  <p className="text-sm text-gray-600 mt-1">
                    {item?.story_stats_label}
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