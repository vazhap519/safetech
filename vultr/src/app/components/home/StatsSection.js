

"use client";

import useFadeIn from "../../hooks/useFadeIn";
import CountUp from "react-countup";

export default function StatsSection({ data = [] }) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`py-28 bg-gradient-to-b from-white via-gray-50 to-white transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-6xl mx-auto px-4 text-center">

        <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-2">
          შედეგები
        </p>

        <h2 className="text-3xl md:text-4xl font-bold text-[#0B3C5D] mb-16">
          ჩვენი მიღწევები
        </h2>

        <div className="grid md:grid-cols-3 gap-10">

          {data.map((item, i) => {
            const number = parseInt(item.value);

            return (
              <div
                key={i}
                className={`group relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-700 hover:-translate-y-2 ${
                  visible
                    ? "opacity-100 translate-y-0"
                    : "opacity-0 translate-y-10"
                }`}
                style={{
                  transitionDelay: `${i * 120}ms`,
                }}
              >

                {/* glow */}
                <div className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 bg-[#00C2A8]/5 blur-xl transition duration-500"></div>

                {/* NUMBER */}
                <p className="relative text-5xl md:text-6xl font-bold text-[#00C2A8]">
                  <span suppressHydrationWarning>
                    {visible ? (
                      <CountUp end={number} duration={2} />
                    ) : (
                      number
                    )}
                  </span>

                  {item.value.includes("+") && "+"}
                </p>

                <div className="relative w-10 h-[2px] bg-gradient-to-r from-[#00C2A8] to-transparent mx-auto mt-5"></div>

                <p className="relative text-gray-500 mt-4 text-sm uppercase tracking-wide">
                  {item.label}
                </p>

              </div>
            );
          })}

        </div>

      </div>
    </section>
  );
}