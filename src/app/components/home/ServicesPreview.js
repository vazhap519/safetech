"use client";

import useFadeIn from "../../hooks/useFadeIn";
import Link from "next/link";
import Image from "next/image";

export default function ServicesPreview({ data }) {
  const services = data?.services || []; // ✅ FIX
  console.log(services)
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className="py-20 bg-white transition-all duration-700"
    >
      <div className="max-w-7xl mx-auto px-4">

        {/* Title */}
        <div
          className={`text-center transition-all duration-700 ${
            visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
          }`}
        >
          <h2 className="text-3xl font-bold text-[#0B3C5D]">
            {data?.servicesSection?.title}
          </h2>
          <p className="text-gray-500 mt-2">
            {data?.servicesSection?.description}
          </p>
        </div>

        {/* Grid */}
        <div className="grid md:grid-cols-3 gap-8 mt-12">

          {services.map((service, i) => (
            <Link key={service.slug} href={`/services/${service.slug}`}>
              <div className="group bg-[#F8FAFC] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] transition-all duration-700">

                <div className="relative overflow-hidden">
                  <Image
                    src={service.image || "/placeholder.jpg"} // 🔥 safe fallback
                    alt={service.title}
                    width={500}
                    height={300}
                    className="w-full h-44 object-cover group-hover:scale-110 transition duration-300"
                  />
                </div>

                <div className="p-6">
                  <h3 className="text-lg font-semibold text-[#0B3C5D]">
                    {service.title}
                  </h3>

                  <p className="text-sm text-gray-600 mt-2">
                    {service.description}
                  </p>

                  <div className="mt-4 text-sm text-[#00C2A8] font-medium">
                    დეტალურად →
                  </div>
                </div>

              </div>
            </Link>
          ))}

        </div>
      </div>
    </section>
  );
}