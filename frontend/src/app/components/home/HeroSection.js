"use client";

import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";
import Image from "next/image";

export default function HeroSection({ data }) {
  if (!data) return null;

  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`py-20 bg-[#0B3C5D] text-white 
      transition-all duration-700 ease-out will-change-transform ${
        visible
          ? "opacity-100 translate-y-0"
          : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

        {/* LEFT CONTENT */}
        <div className="text-center md:text-left">

          {/* TITLE */}
          <h1 className="text-3xl md:text-5xl font-bold leading-tight">
            {data.title}
          </h1>

          {/* DESCRIPTION */}
          <p className="mt-4 text-gray-300 text-lg">
            {data.description}
          </p>

          {/* BUTTONS */}
          <div className="mt-6 flex flex-wrap gap-4 justify-center md:justify-start">
            
            {/* CALL BUTTON */}
            <a
              href={`tel:${data?.call_number || ""}`}
              className="bg-[#00C2A8] text-white px-6 py-3 rounded-xl hover:bg-[#00a892] transition shadow-md"
            >
              📞 {data.call_button}
            </a>

            {/* SERVICE BUTTON */}
            <Link
              href="/services"
              className="border-2 border-white text-white px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition"
            >
              {data.service_button}
            </Link>
          </div>

          {/* TRUST SIGNALS */}
          <div className="mt-6 text-sm text-gray-300 flex flex-wrap gap-4 justify-center md:justify-start">
            {data?.list?.map((li, i) => (
              <span key={i}>✔ {li.text}</span>
            ))}
          </div>

        </div>

        {/* RIGHT IMAGE */}
        <div className="relative w-full h-[300px] md:h-[450px]">

<div className="relative w-full h-[300px] md:h-[450px] overflow-hidden rounded-2xl">

  {data?.image ? (
    <Image
      src={data.image}
      alt={data.title || "Hero image"}
      fill
      priority
      quality={70} // 🔥 performance boost
      sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 600px"
      className="object-cover transition-transform duration-700 hover:scale-105"
    />
  ) : (
    <div className="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-gray-300 text-sm">
      Image not available
    </div>
  )}

</div>
        </div>

      </div>
    </section>
  );
}