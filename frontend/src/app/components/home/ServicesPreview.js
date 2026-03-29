

"use client";

import useFadeIn from "../../hooks/useFadeIn";
import Link from "next/link";
import Image from "next/image";

export default function ServicesPreview({ data }) {
  const services = data?.services || [];
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className="relative py-28 bg-gradient-to-b from-white to-gray-50 transition-all duration-700 overflow-hidden"
    >
      {/* 🔥 BACKGROUND GLOW */}
      <div className="absolute inset-0 pointer-events-none">
        <div className="absolute top-20 left-10 w-72 h-72 bg-[#00C2A8]/10 blur-[120px]" />
        <div className="absolute bottom-20 right-10 w-72 h-72 bg-[#00C2FF]/10 blur-[120px]" />
      </div>

      <div className="max-w-7xl mx-auto px-4 relative">

        {/* TITLE */}
        <div
          className={`text-center max-w-2xl mx-auto transition-all duration-700 ${
            visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
          }`}
        >
          <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-2">
            სერვისები
          </p>

          <h2 className="text-3xl md:text-4xl font-bold text-[#0B3C5D]">
            {data?.servicesSection?.title}
          </h2>

          <p className="text-gray-500 mt-4 leading-relaxed">
            {data?.servicesSection?.description}
          </p>
        </div>

        {/* GRID */}
        <div className="grid md:grid-cols-3 gap-10 mt-20">

          {services.map((service, i) => (
            <Link key={service.slug} href={`/services/${service.slug}`}>

              <div
                className={`group relative bg-white rounded-3xl overflow-hidden border border-gray-100 
                shadow-sm hover:shadow-2xl transition-all duration-700 hover:-translate-y-3
                ${
                  visible
                    ? "opacity-100 translate-y-0"
                    : "opacity-0 translate-y-10"
                }`}
                style={{
                  transitionDelay: `${i * 120}ms`,
                }}
              >

                {/* IMAGE */}
                <div className="relative h-[220px] overflow-hidden">

                  <Image
                    src={service.image || "/placeholder.jpg"}
                    alt={service.title}
                    fill
                    className="object-cover transition duration-700 group-hover:scale-110"
                  />

                  {/* overlay */}
                  <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>

                  {/* 🔥 TOP SHINE */}
                  <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition">
                    <div className="w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-[shine_1.5s_linear]"></div>
                  </div>
                </div>

                {/* CONTENT */}
                <div className="p-6">

                  <h3 className="text-lg font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                    {service.title}
                  </h3>

                  <p className="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-3">
                    {service.description}
                  </p>

                  {/* CTA */}
                  <div className="mt-6 flex items-center justify-between">

                    <span className="text-sm text-[#00C2A8] font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                      დეტალურად →
                    </span>

                    <div className="w-9 h-9 rounded-full bg-[#00C2A8]/10 flex items-center justify-center 
                    group-hover:bg-[#00C2A8] group-hover:scale-110 transition">

                      <span className="text-[#00C2A8] group-hover:text-white text-sm">
                        →
                      </span>
                    </div>

                  </div>

                </div>

                {/* 🔥 GLOW BORDER */}
                <div className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 
                border border-[#00C2A8]/30 blur-[1px] transition"></div>

              </div>

            </Link>
          ))}

        </div>
      </div>
    </section>
  );
}