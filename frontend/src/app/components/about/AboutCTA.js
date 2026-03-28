// "use client";

// import Link from "next/link";
// import useFadeIn from "@/app/hooks/useFadeIn";

// export default function AboutCTA({ Cta }) {
//   const [ref, visible] = useFadeIn();

//   return (
//     <section
//       ref={ref}
//       className={`relative py-20 text-white overflow-hidden transition-all duration-700 ${
//         visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
//       }`}
//     >
//       {/* 🔥 BACKGROUND */}
//       <div className="absolute inset-0 bg-[#0B3C5D]" />

//       {/* 🔥 GLOW */}
//       <div className="absolute -top-20 right-0 w-72 h-72 bg-[#00C2A8]/20 blur-3xl rounded-full" />

//       <div className="relative max-w-4xl mx-auto px-4 text-center">

//         {/* 🔥 TITLE */}
//         <h2 className="text-3xl md:text-4xl font-bold">
//           {Cta?.title}
//         </h2>

//         {/* 🔥 TEXT */}
//         <p className="mt-4 text-gray-300">
//           {Cta?.description}
//         </p>

//         {/* 🔥 CTA BUTTONS */}
//         <div className="mt-8 flex justify-center gap-4 flex-wrap">

//           <a
//             href={`tel:+${Cta?.phone}`}
//             className="bg-[#00C2A8] px-6 py-3 rounded-xl hover:bg-[#00a892] transition shadow-md hover:shadow-xl active:scale-95"
//           >
//             📞 დაგვირეკე
//           </a>

//           <Link
//             href="/contact"
//             className="border border-white/70 px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition active:scale-95"
//           >
//             შეტყობინების გაგზავნა
//           </Link>

//         </div>

//         {/* 🔥 TRUST LINE (DYNAMIC) */}
//         <div className="mt-6 flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-400">

//           {Cta?.trust?.map((item, i) => (
//             <span
//               key={i}
//               className="flex items-center gap-2 hover:text-white transition"
//             >
//               <span className="text-[#00C2A8]">✔</span>
//               {item?.cta_trust_title}
//             </span>
//           ))}

//         </div>

//       </div>
//     </section>
//   );
// }



"use client";

import Link from "next/link";
import useFadeIn from "@/app/hooks/useFadeIn";

export default function AboutCTA({ Cta }) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`
        relative 
        py-24 
        bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B]
        text-white 
        overflow-hidden 
        transition-all duration-700
        ${visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"}
      `}
    >
      {/* 🔥 CENTER GLOW */}
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(0,224,184,0.25),transparent_60%)] pointer-events-none" />

      <div className="relative max-w-4xl mx-auto px-6 text-center">

        {/* TITLE */}
        <h2 className="text-4xl md:text-5xl font-bold leading-tight">
          {Cta?.title || "მზად ხარ შედეგებისთვის?"}
        </h2>

        {/* DESC */}
        <p className="mt-6 text-lg text-white/80 max-w-xl mx-auto">
          {Cta?.description || "მიიღე უფასო კონსულტაცია და დაიწყე გაუმჯობესება დღესვე"}
        </p>

        {/* CTA BUTTONS */}
        <div className="mt-10 flex flex-col sm:flex-row justify-center gap-4">

          <a
            href={`tel:+${Cta?.phone}`}
            className="
              bg-[#00E0B8]
              text-black
              px-10 py-5
              rounded-2xl
              font-semibold
              text-lg
              shadow-[0_10px_40px_rgba(0,224,184,0.4)]
              hover:scale-105
              transition-all duration-300
            "
          >
            📞 დაგვირეკე ახლა
          </a>

          <Link
            href="/contact"
            className="
              border border-white/20
              px-10 py-5
              rounded-2xl
              text-white/90
              hover:bg-white/10
              transition-all duration-300
            "
          >
            შეტყობინება
          </Link>

        </div>

        {/* TRUST LINE */}
        <div className="mt-6 text-sm text-white/60">
          ✔ უფასო კონსულტაცია • ✔ სწრაფი რეაგირება • ✔ გამოცდილება
        </div>

        {/* OPTIONAL EXTRA TRUST */}
        <div className="mt-3 flex flex-wrap justify-center gap-4 text-xs text-white/40">
          {Cta?.trust?.map((item, i) => (
            <span key={i}>
              {item?.cta_trust_title}
            </span>
          ))}
        </div>

      </div>
    </section>
  );
}