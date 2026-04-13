
"use client";

import useFadeIn from "@/app/hooks/useFadeIn";
import * as FaIcons from "react-icons/fa";

export default function AboutWhyUs({ Why }) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`py-24 bg-white transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-7xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            {Why?.title}
          </h2>

          <p className="mt-4 text-gray-600 max-w-2xl mx-auto">
            {Why?.description}
          </p>
        </div>

        {/* GRID */}
        <div className="grid md:grid-cols-3 gap-8">

          {Why?.items?.map((item, i) => {
            const Icon = FaIcons[item.why_us_icons];

            return (
              <div
                key={i}
                className={`group bg-gray-50 border border-gray-200 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl ${
                  visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
                }`}
                style={{ transitionDelay: `${i * 120}ms` }}
              >

                {/* ICON BOX */}
                <div className="w-12 h-12 flex items-center justify-center rounded-xl bg-[#00C2A8]/10 text-[#00C2A8] text-xl">
                  {Icon ? <Icon /> : "⚡"}
                </div>

                {/* TITLE */}
                <h3 className="mt-4 text-lg font-semibold text-gray-900 group-hover:text-[#00C2A8] transition">
                  {item.title}
                </h3>

                {/* DESC */}
                <p className="text-gray-600 mt-2 leading-relaxed">
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
