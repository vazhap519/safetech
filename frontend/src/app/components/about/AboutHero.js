"use client";

import Link from "next/link";
import useFadeIn from "@/app/hooks/useFadeIn";
import Image from "next/image";

export default function AboutHero({Hero}) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`relative bg-[#0B3C5D] text-white py-24 overflow-hidden transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      {/* 🔥 LIGHT GLOW */}
      <div className="absolute -top-20 -left-20 w-72 h-72 bg-[#00C2A8]/20 blur-3xl rounded-full"></div>

      {/* 🔥 GRADIENT OVERLAY */}
      <div className="absolute inset-0 bg-gradient-to-r from-[#0B3C5D] via-[#0B3C5D]/90 to-transparent"></div>

      <div className="relative max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">

        {/* LEFT */}
        <div>
          <h1 className="text-4xl md:text-5xl font-bold leading-tight">
          {Hero?.title}
          </h1>

        <div
  className="mt-5 text-gray-300 text-lg leading-relaxed space-y-3 [&>p]:mb-2 [&>p]:leading-relaxed"
  dangerouslySetInnerHTML={{
    __html: Hero?.description || "",
  }}
/>
          {/* CTA */}
          <div className="mt-8 flex gap-4 flex-wrap">

            <Link
              href="/services"
              className="bg-[#00C2A8] px-6 py-3 rounded-xl  hover:bg-[#00a892] transition shadow-md hover:shadow-xl active:scale-95"
            >
              სერვისების ნახვა
            </Link>

            <Link
              href="/contact"
              className="border border-white/70 px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition active:scale-95"
            >
              დაგვიკავშირდი
            </Link>

          </div>

          {/* TRUST */}
          {/* <div className="mt-8 flex flex-wrap gap-5 text-sm text-gray-400">
            {Hero.hero_trust_list.map(Key,item)=>{
              return(
          <span className="hover:text-white transition">✔ გამოცდილება</span>
              )
            }}
  
            <span className="hover:text-white transition">✔ სწრაფი მომსახურება</span>
            <span className="hover:text-white transition">✔ მთელი საქართველო</span>
          </div> */}

          <div className="mt-8 flex flex-wrap gap-5 text-sm text-gray-400">
  {Hero?.trust_list ?.map((item, index) => (
    <span
      key={index}
      className="flex items-center gap-2 hover:text-white transition"
    >
      <span className="text-[#00C2A8]">✔</span>
    {item?.hero_trust_list_title || ""}
  </span>
  ))}
</div>
        </div>

        {/* RIGHT */}
      <div className="relative w-full h-[350px] md:h-[420px] group">
<Image
  src={Hero?.image || "/services/1.jpg"}
  alt={
    Hero?.title
      ? `${Hero.title} - Safetech`
      : "Safetech about section image"
  }
  fill
  priority // 🔥 LCP fix (hero image)
  fetchPriority="high" // 🔥 browser optimization
  quality={60} // 🔥 smaller image size
  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 600px"
  className="rounded-2xl object-cover transition duration-500 group-hover:scale-105"
/>

  {/* overlay */}
  <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent rounded-2xl"></div>

  {/* badge */}
  <div className="absolute bottom-5 left-5 bg-white text-[#0B3C5D] px-4 py-2 rounded-lg shadow-lg text-sm font-medium">
    ⭐ {Hero?.badge}
  </div>
</div>

      </div>
    </section>
  );
}