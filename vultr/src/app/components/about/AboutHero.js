


"use client";
import Link from "next/link";
import useFadeIn from "@/app/hooks/useFadeIn";
import Image from "next/image";

export default function AboutHero({ Hero }) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`relative py-24 md:py-32 bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B] text-white overflow-hidden transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      {/* 🔥 SOFT GLOW */}
      <div className="absolute -top-32 -left-32 w-[400px] h-[400px] bg-[#00E0B8]/20 blur-3xl rounded-full" />

      {/* 🔥 SECOND GLOW */}
      <div className="absolute bottom-0 right-0 w-[300px] h-[300px] bg-[#00C2FF]/10 blur-3xl rounded-full" />

      <div className="relative max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">

        {/* LEFT */}
        <div>

          <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
            {Hero?.title}
          </h1>

          <div
            className="mt-6 text-lg text-white/80 leading-relaxed max-w-xl space-y-3 [&>p]:mb-2"
            suppressHydrationWarning
            dangerouslySetInnerHTML={{
              __html: Hero?.description || "",
            }}
          />

          <div className="mt-10 flex flex-wrap gap-4">

            <Link
              href="/services"
              className="bg-[#00E0B8] text-black px-8 py-4 rounded-2xl font-semibold shadow-[0_10px_40px_rgba(0,224,184,0.35)] hover:scale-105 transition-all duration-300"
            >
              🚀 სერვისების ნახვა
            </Link>

            <Link
              href="/contact"
              className="border border-white/20 px-8 py-4 rounded-2xl text-white/90 hover:bg-white/10 transition-all duration-300"
            >
              დაგვიკავშირდი
            </Link>

          </div>

          <div className="mt-10 flex flex-wrap gap-5 text-sm text-white/60">

            {Hero?.trust_list?.map((item, index) => (
              <span
                key={index}
                className="flex items-center gap-2 hover:text-white transition"
              >
                <span className="text-[#00E0B8]">✔</span>
                {item?.hero_trust_list_title || ""}
              </span>
            ))}

          </div>

        </div>

        {/* RIGHT */}
        <div className="relative w-full h-[360px] md:h-[460px]">

          <div className="absolute inset-0 rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm" />

          <Image
            src={Hero?.image || "/placeholder.jpg"}
            alt={
              Hero?.title
                ? `${Hero.title} - Safetech`
                : "Safetech about section image"
            }
            fill
            priority
            fetchPriority="high"
            quality={70}
            sizes="(max-width: 768px) 100vw, 50vw"
            className="object-cover rounded-3xl shadow-2xl"
          />

          <div className="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-3xl" />

          <div className="absolute bottom-6 left-6 bg-white text-[#071A2B] px-5 py-2 rounded-xl shadow-xl text-sm font-semibold">
            ⭐ {Hero?.badge}
          </div>

        </div>

      </div>
    </section>
  );
}