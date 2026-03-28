"use client";

import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";
import Image from "next/image";

export default function HeroSection({ data }) {
  if (!data) return null;

  const [ref, visible] = useFadeIn();

  return (
//     <section
//       ref={ref}
//       className={`py-20 bg-[#0B3C5D] text-white 
//       transition-all duration-700 ease-out will-change-transform ${
//         visible
//           ? "opacity-100 translate-y-0"
//           : "opacity-0 translate-y-10"
//       }`}
//     >
//       <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

//         {/* LEFT CONTENT */}
//         <div className="text-center md:text-left">

//           {/* TITLE */}
//           <h1 className="text-3xl md:text-5xl font-bold leading-tight">
//             {data.title}
//           </h1>

//           {/* DESCRIPTION */}
//           <p className="mt-4 text-gray-300 text-lg">
//             {data.description}
//           </p>

//           {/* BUTTONS */}
//           <div className="mt-6 flex flex-wrap gap-4 justify-center md:justify-start">
            
//             {/* CALL BUTTON */}
//             <a
//               href={`tel:${data?.call_number || ""}`}
//               className="bg-[#00C2A8] text-white px-6 py-3 rounded-xl hover:bg-[#00a892] transition shadow-md"
//             >
//               📞 {data.call_button}
//             </a>

//             {/* SERVICE BUTTON */}
//             <Link
//               href="/services"
//               className="border-2 border-white text-white px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition"
//             >
//               {data.service_button}
//             </Link>
//           </div>

//           {/* TRUST SIGNALS */}
//           <div className="mt-6 text-sm text-gray-300 flex flex-wrap gap-4 justify-center md:justify-start">
//             {data?.list?.map((li, i) => (
//               <span key={i}>✔ {li.text}</span>
//             ))}
//           </div>

//         </div>

//         {/* RIGHT IMAGE */}
//         <div className="relative w-full h-[300px] md:h-[450px]">

// <div className="relative w-full h-[300px] md:h-[450px] overflow-hidden rounded-2xl">

//   {data?.image ? (
//     <Image
//       src={data.image}
//       alt={data.title || "Hero image"}
//       fill
//       priority
//       quality={70} // 🔥 performance boost
//       sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 600px"
//       className="object-cover transition-transform duration-700 hover:scale-105"
//     />
//   ) : (
//     <div className="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-gray-300 text-sm">
//       Image not available
//     </div>
//   )}

// </div>
//         </div>

//       </div>
//     </section>


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

      {/* label */}
      <p className="text-[#00C2A8] uppercase tracking-widest text-sm mb-3">
        Premium Service
      </p>

      {/* TITLE */}
      <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
        {data.title}
      </h1>

      {/* DESCRIPTION */}
      <p className="mt-6 text-gray-300 text-lg max-w-xl leading-relaxed">
        {data.description}
      </p>

      {/* BUTTONS */}
      <div className="mt-8 flex flex-wrap gap-4">

        <a
          href={`tel:${data?.call_number || ""}`}
          className="bg-[#00C2A8] px-7 py-3 rounded-xl font-medium shadow-lg 
          hover:shadow-[#00C2A8]/40 hover:scale-105 active:scale-95 transition-all"
        >
          📞 {data.call_button}
        </a>

        <Link
          href="/services"
          className="px-7 py-3 rounded-xl border border-white/20 backdrop-blur 
          hover:bg-white hover:text-[#0B3C5D] transition-all"
        >
          {data.service_button}
        </Link>

      </div>

      {/* TRUST POINTS */}
      <div className="mt-6 flex flex-wrap gap-4 text-sm text-gray-400">
        {data?.list?.map((li, i) => (
          <span key={i} className="flex items-center gap-2 hover:text-white transition">
            ✔ {li.text}
          </span>
        ))}
      </div>

    </div>

    {/* RIGHT */}
    <div className="relative">

      {/* glow behind image */}
      <div className="absolute inset-0 bg-gradient-to-tr from-[#00C2A8]/30 to-transparent blur-3xl"></div>

      {/* card */}
      <div className="relative w-full aspect-[16/10] rounded-3xl overflow-hidden border border-white/10 backdrop-blur-xl shadow-2xl group">

        <Image
          src={data.image}
          alt="Hero image"
          fill
          priority
          sizes="(max-width: 768px) 100vw, 50vw"
          className="object-contain transition duration-700 group-hover:scale-110"
        />

        {/* 🔥 floating gradient overlay */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

      </div>

    </div>

  </div>
</section>
  );
}