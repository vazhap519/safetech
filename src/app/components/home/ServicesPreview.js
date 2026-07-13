"use client";

import Image from "next/image";
import Link from "next/link";

import useFadeIn from "../../hooks/useFadeIn";

export default function ServicesPreview({ data }) {
  const [ref, visible] = useFadeIn();
  const services = data?.services || [];

  return (
    <section ref={ref} className="bg-white py-20 transition-all duration-700">
      <div className="mx-auto max-w-7xl px-4">
        <div
          className={`text-center transition-all duration-700 ${
            visible ? "translate-y-0 opacity-100" : "translate-y-10 opacity-0"
          }`}
        >
          <h2 className="text-3xl font-bold text-[#0B3C5D]">
            {data?.servicesSection?.title}
          </h2>
          <p className="mt-2 text-gray-500">
            {data?.servicesSection?.description}
          </p>
        </div>

        <div className="mt-12 grid gap-8 md:grid-cols-3">
          {services.map((service) => (
            <Link key={service.slug} href={`/services/${service.slug}`}>
              <article className="group overflow-hidden rounded-2xl bg-[#F8FAFC] shadow-sm transition-all duration-700 hover:-translate-y-1 hover:scale-[1.02] hover:shadow-xl">
                <div className="relative h-44 overflow-hidden">
                  <Image
                    src={service.image || "/brand-preview.svg"}
                    alt={service.title || "Service image"}
                    fill
                    sizes="(max-width: 768px) 100vw, 33vw"
                    className="object-cover transition duration-300 group-hover:scale-110"
                  />
                </div>

                <div className="p-6">
                  <h3 className="text-lg font-semibold text-[#0B3C5D]">
                    {service.title}
                  </h3>

                  <p className="mt-2 text-sm text-gray-600">
                    {service.description}
                  </p>

                  <div className="mt-4 text-sm font-medium text-[#00C2A8]">
                    Learn more
                  </div>
                </div>
              </article>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}
