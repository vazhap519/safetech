"use client";

import Link from "next/link";
import useFadeIn from "@/app/hooks/useFadeIn";

export default function AboutHero() {
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
            Safetech — შენი სანდო IT პარტნიორი
          </h1>

          <p className="mt-5 text-gray-300 text-lg leading-relaxed">
            ჩვენ ვთავაზობთ თანამედროვე IT გადაწყვეტილებებს, უსაფრთხოების სისტემებს 
            და ქსელურ სერვისებს როგორც ბიზნესისთვის, ასევე კერძო მომხმარებლებისთვის.
          </p>

          <p className="mt-4 text-gray-400 text-sm">
            მრავალწლიანი გამოცდილება გვაძლევს საშუალებას შევქმნათ უსაფრთხო, 
            სტაბილური და სწრაფი ინფრასტრუქტურა.
          </p>

          {/* CTA */}
          <div className="mt-8 flex gap-4 flex-wrap">

            <Link
              href="/services"
              className="bg-[#00C2A8] px-6 py-3 rounded-xl 
              hover:bg-[#00a892] transition 
              shadow-md hover:shadow-xl active:scale-95"
            >
              სერვისების ნახვა
            </Link>

            <Link
              href="/contact"
              className="border border-white/70 px-6 py-3 rounded-xl 
              hover:bg-white hover:text-[#0B3C5D] 
              transition active:scale-95"
            >
              დაგვიკავშირდი
            </Link>

          </div>

          {/* TRUST */}
          <div className="mt-8 flex flex-wrap gap-5 text-sm text-gray-400">
            <span className="hover:text-white transition">✔ გამოცდილება</span>
            <span className="hover:text-white transition">✔ სწრაფი მომსახურება</span>
            <span className="hover:text-white transition">✔ მთელი საქართველო</span>
          </div>
        </div>

        {/* RIGHT */}
        <div className="relative group">

          <img
            src="/services/1.jpg"
            alt="Safetech IT სერვისები"
            className="rounded-2xl shadow-xl w-full object-cover 
            transition duration-500 group-hover:scale-105"
          />

          {/* 🔥 IMAGE GRADIENT OVERLAY */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent rounded-2xl"></div>

          {/* BADGE */}
          <div className="absolute bottom-5 left-5 bg-white text-[#0B3C5D] px-4 py-2 rounded-lg shadow-lg text-sm font-medium">
            ⭐ 100+ კმაყოფილი კლიენტი
          </div>

        </div>

      </div>
    </section>
  );
}