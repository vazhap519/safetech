"use client";

import useFadeIn from "@/app/hooks/useFadeIn";
import * as FaIcons from "react-icons/fa";

export default function AboutWhyUs({ Why }) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`py-20 bg-white transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-7xl mx-auto px-4">

        {/* 🔥 TITLE */}
        <div className="text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-[#0B3C5D]">
            {Why?.title}
          </h2>

          <p className="mt-4 text-gray-600 max-w-xl mx-auto">
            {Why?.description}
          </p>
        </div>

        {/* 🔥 GRID */}
        <div className="mt-12 grid md:grid-cols-3 gap-6">

          {Why?.items?.map((item, i) => {
            const Icon = FaIcons[item.why_us_icons];

            return (
              <div
                key={i}
                className={`group bg-[#F8FAFC] p-6 rounded-2xl shadow-sm 
                hover:shadow-xl hover:-translate-y-1 transition-all duration-500
                ${
                  visible
                    ? "opacity-100 translate-y-0"
                    : "opacity-0 translate-y-10"
                }`}
                style={{ transitionDelay: `${i * 120}ms` }}
              >

                {/* 🔥 ICON */}
                <div className="text-3xl text-[#00C2A8]">
                  {Icon ? <Icon /> : "⚡"}
                </div>

                {/* 🔥 TITLE */}
                <h3 className="mt-4 font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                  {item.title}
                </h3>

                {/* 🔥 DESC */}
                <p className="text-sm text-gray-600 mt-2 leading-relaxed">
                  {item.desc}
                </p>

              </div>
            );
          })}

        </div>

      </div>
    </section>
  );
}