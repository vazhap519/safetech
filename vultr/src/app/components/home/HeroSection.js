

"use client";

import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";
import Image from "next/image";

export default function HeroSection({ data }) {
    const [ref, visible] = useFadeIn();
  if (!data) return null;



  // ✅ FIX ONLY — არ ვეხებით UI-ს
  const imageSrc =
    typeof data?.image === "string" && data.image.trim() !== ""
      ? data.image
      : null;

  return (
    <section
      ref={ref}
      className={`relative py-28 bg-gradient-to-br from-[#071E2F] via-[#0B3C5D] to-[#0E4F73] text-white overflow-hidden transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      {/* 🔥 BACKGROUND GLOW */}
      <div className="absolute inset-0 pointer-events-none">
        <div className="absolute top-[-100px] left-[-100px] w-[400px] h-[400px] bg-[#00C2A8]/20 blur-[120px]" />
        <div className="absolute bottom-[-100px] right-[-100px] w-[400px] h-[400px] bg-[#00C2FF]/20 blur-[120px]" />
      </div>

      <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center relative">

        {/* LEFT */}
        <div>
          <p className="text-[#00C2A8] uppercase tracking-widest text-sm mb-3">
            Premium Service
          </p>

          <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
            {data.title}
          </h1>

          <p className="mt-6 text-gray-300 text-lg max-w-xl leading-relaxed">
            {data.description}
          </p>

          <div className="mt-8 flex flex-wrap gap-4">

            {/* ✅ FIX: არ ვაძლევთ ცარიელ tel */}
            {data?.call_number && (
              <a
                href={`tel:${data.call_number}`}
                className="bg-[#00C2A8] px-7 py-3 rounded-xl font-medium shadow-lg 
                hover:shadow-[#00C2A8]/40 hover:scale-105 active:scale-95 transition-all"
              >
                📞 {data.call_button}
              </a>
            )}

            <Link
              href="/services"
              className="px-7 py-3 rounded-xl border border-white/20 backdrop-blur 
              hover:bg-white hover:text-[#0B3C5D] transition-all"
            >
              {data.service_button}
            </Link>
          </div>

          <div className="mt-6 flex flex-wrap gap-4 text-sm text-gray-400">
            {Array.isArray(data?.list) &&
              data.list.map((li, i) => (
                <span key={i} className="flex items-center gap-2 hover:text-white transition">
                  ✔ {li.text}
                </span>
              ))}
          </div>
        </div>

        {/* RIGHT */}
        <div className="relative">
          <div className="absolute inset-0 bg-gradient-to-tr from-[#00C2A8]/30 to-transparent blur-3xl"></div>

          <div className="relative w-full aspect-[16/10] rounded-3xl overflow-hidden border border-white/10 backdrop-blur-xl shadow-2xl group">

            {/* ✅ FIX: მხოლოდ მაშინ render როცა src სწორია */}
            {imageSrc && (
              <Image
                src={imageSrc}
                alt="Hero image"
                fill
                priority
                sizes="(max-width: 768px) 100vw, 50vw"
                className="object-contain transition duration-700 group-hover:scale-110"
              />
            )}

            <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
          </div>
        </div>

      </div>
    </section>
  );
}