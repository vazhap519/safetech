"use client";

import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";

export default function HeroSection({data}) {

   if (!data) return null;
     const [ref, visible] = useFadeIn();
  return (
    <section
    ref={ref}
  className={`py-20 bg-[#0B3C5D] text-white text-center 
  transition-all duration-700 ease-out will-change-transform ${
    visible
      ? "opacity-100 translate-y-0"
      : "opacity-0 translate-y-10"
  }`}
    >
      <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

        {/* LEFT CONTENT */}
        <div>
          <h1 className="text-4xl md:text-2xl text-gray-300 font-bold text-[#0B3C5D] leading-tight">
            {/* კამერების მონტაჟი და IT სერვისები თბილისში */}
            {data.title}
          </h1>

          <p className="mt-4 text-gray-300 text-lg">
            {/* Safetech გთავაზობთ უსაფრთხოების სისტემებს,
             ქსელურ სერვისებს
              და სრულ IT მხარდაჭერას სწრაფად და პროფესიონალურად. */}
              {data.description}
          </p>

          {/* CTA BUTTONS */}
          <div className="mt-6 flex flex-wrap gap-4">
            <a
              href={`tel:${data.call__number}`}
              className="bg-[#00C2A8] text-white px-6 py-3 rounded-xl hover:bg-[#00a892] transition shadow-md"
            >
              📞 {data.call_button}
            </a>

            <Link
              href="/services"
  className="border-2 border-white text-white px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition"            >
              {data.service_button}
            </Link>
          </div>

          {/* TRUST SIGNALS */}
          <div className="mt-6 text-sm text-gray-300 flex flex-wrap gap-4">
           {data.list?.map((li, i) => (
  <span key={i}>✔ {li.text}</span>
))}
            
          </div>
        </div>

        {/* RIGHT IMAGE */}
        <div className="relative">
          <img
            src={data.image}
            alt={data.title}
            className="rounded-2xl text-gray-500 shadow-lg w-full object-cover"
          />
        </div>

      </div>
    </section>
  );
}