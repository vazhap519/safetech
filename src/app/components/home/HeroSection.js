// // "use client";

// // import Link from "next/link";
// // import useFadeIn from "../../hooks/useFadeIn";
// // import Image from "next/image";

// // export default function HeroSection({ data }) {
// //   if (!data) return null;

// //   const [ref, visible] = useFadeIn();

// //   return (

// // <section
// //   ref={ref}
// //   className={`relative py-28 bg-gradient-to-br from-[#071E2F] via-[#0B3C5D] to-[#0E4F73] text-white overflow-hidden transition-all duration-700 ${
// //     visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
// //   }`}
// // >

// //   {/* 🔥 BACKGROUND GLOW */}
// //   <div className="absolute inset-0 pointer-events-none">
// //     <div className="absolute top-[-100px] left-[-100px] w-[400px] h-[400px] bg-[#00C2A8]/20 blur-[120px]" />
// //     <div className="absolute bottom-[-100px] right-[-100px] w-[400px] h-[400px] bg-[#00C2FF]/20 blur-[120px]" />
// //   </div>

// //   <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center relative">

// //     {/* LEFT */}
// //     <div>

// //       {/* label */}
// //       <p className="text-[#00C2A8] uppercase tracking-widest text-sm mb-3">
// //         Premium Service
// //       </p>

// //       {/* TITLE */}
// //       <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
// //         {data.title}
// //       </h1>

// //       {/* DESCRIPTION */}
// //       <p className="mt-6 text-gray-300 text-lg max-w-xl leading-relaxed">
// //         {data.description}
// //       </p>

// //       {/* BUTTONS */}
// //       <div className="mt-8 flex flex-wrap gap-4">

// //         <a
// //           href={`tel:${data?.call_number || ""}`}
// //           className="bg-[#00C2A8] px-7 py-3 rounded-xl font-medium shadow-lg 
// //           hover:shadow-[#00C2A8]/40 hover:scale-105 active:scale-95 transition-all"
// //         >
// //           📞 {data.call_button}
// //         </a>

// //         <Link
// //           href="/services"
// //           className="px-7 py-3 rounded-xl border border-white/20 backdrop-blur 
// //           hover:bg-white hover:text-[#0B3C5D] transition-all"
// //         >
// //           {data.service_button}
// //         </Link>

// //       </div>

// //       {/* TRUST POINTS */}
// //       <div className="mt-6 flex flex-wrap gap-4 text-sm text-gray-400">
// //         {data?.list?.map((li, i) => (
// //           <span key={i} className="flex items-center gap-2 hover:text-white transition">
// //             ✔ {li.text}
// //           </span>
// //         ))}
// //       </div>

// //     </div>

// //     {/* RIGHT */}
// //     <div className="relative">

// //       {/* glow behind image */}
// //       <div className="absolute inset-0 bg-gradient-to-tr from-[#00C2A8]/30 to-transparent blur-3xl"></div>

// //       {/* card */}
// //       <div className="relative w-full aspect-[16/10] rounded-3xl overflow-hidden border border-white/10 backdrop-blur-xl shadow-2xl group">

// //         <Image
// //           src={data.image}
// //           alt="Hero image"
// //           fill
// //           priority
// //           sizes="(max-width: 768px) 100vw, 50vw"
// //           className="object-contain transition duration-700 group-hover:scale-110"
// //         />

// //         {/* 🔥 floating gradient overlay */}
// //         <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

// //       </div>

// //     </div>

// //   </div>
// // </section>
// //   );
// // }



// "use client";

// import Link from "next/link";
// import Image from "next/image";
// import { useEffect, useState } from "react";
// import useFadeIn from "../../hooks/useFadeIn";

// /**
//  * ✅ Validate Hero Data (Senior approach)
//  */
// function isValidHero(data) {
//   return (
//     data &&
//     typeof data.title === "string" &&
//     data.title.trim() !== ""
//   );
// }

// export default function HeroSection({ data }) {
//   // ❌ თუ საერთოდ არასწორი dataა → არაფერი render
//   if (!isValidHero(data)) return null;

//   /**
//    * ✅ Prevent hydration mismatch
//    */
//   const [mounted, setMounted] = useState(false);
//   useEffect(() => setMounted(true), []);

//   const [ref, visible] = useFadeIn();

//   /**
//    * ✅ Safe values
//    */
//   const imageSrc =
//     typeof data?.image === "string" && data.image.trim() !== ""
//       ? data.image
//       : null;

//   const phone =
//     typeof data?.call_number === "string" && data.call_number.trim() !== ""
//       ? data.call_number
//       : null;

//   const list = Array.isArray(data?.list) ? data.list : [];

//   if (!mounted) return null;

//   return (
//     <section
//       ref={ref}
//       className={`relative py-28 bg-gradient-to-br from-[#071E2F] via-[#0B3C5D] to-[#0E4F73] text-white overflow-hidden transition-all duration-700 ${
//         visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
//       }`}
//     >
//       {/* 🔥 BACKGROUND GLOW */}
//       <div className="absolute inset-0 pointer-events-none">
//         <div className="absolute top-[-100px] left-[-100px] w-[400px] h-[400px] bg-[#00C2A8]/20 blur-[120px]" />
//         <div className="absolute bottom-[-100px] right-[-100px] w-[400px] h-[400px] bg-[#00C2FF]/20 blur-[120px]" />
//       </div>

//       <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center relative">
        
//         {/* LEFT */}
//         <div>
//           {/* LABEL */}
//           {data?.label && (
//             <p className="text-[#00C2A8] uppercase tracking-widest text-sm mb-3">
//               {data.label}
//             </p>
//           )}

//           {/* TITLE */}
//           <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
//             {data.title}
//           </h1>

//           {/* DESCRIPTION */}
//           {data?.description && (
//             <p className="mt-6 text-gray-300 text-lg max-w-xl leading-relaxed">
//               {data.description}
//             </p>
//           )}

//           {/* BUTTONS */}
//           <div className="mt-8 flex flex-wrap gap-4">
            
//             {/* 📞 CALL BUTTON */}
//             {phone && (
//               <a
//                 href={`tel:${phone}`}
//                 className="bg-[#00C2A8] px-7 py-3 rounded-xl font-medium shadow-lg 
//                 hover:shadow-[#00C2A8]/40 hover:scale-105 active:scale-95 transition-all"
//               >
//                 📞 {data?.call_button || "Call Now"}
//               </a>
//             )}

//             {/* SERVICES BUTTON */}
//             {data?.service_button && (
//               <Link
//                 href="/services"
//                 className="px-7 py-3 rounded-xl border border-white/20 backdrop-blur 
//                 hover:bg-white hover:text-[#0B3C5D] transition-all"
//               >
//                 {data.service_button}
//               </Link>
//             )}
//           </div>

//           {/* TRUST POINTS */}
//           {list.length > 0 && (
//             <div className="mt-6 flex flex-wrap gap-4 text-sm text-gray-400">
//               {list.map((li, i) => (
//                 <span
//                   key={i}
//                   className="flex items-center gap-2 hover:text-white transition"
//                 >
//                   ✔ {li?.text || ""}
//                 </span>
//               ))}
//             </div>
//           )}
//         </div>

//         {/* RIGHT */}
//         <div className="relative">
          
//           {/* glow */}
//           <div className="absolute inset-0 bg-gradient-to-tr from-[#00C2A8]/30 to-transparent blur-3xl" />

//           {/* card */}
//           <div className="relative w-full aspect-[16/10] rounded-3xl overflow-hidden border border-white/10 backdrop-blur-xl shadow-2xl group">

//             {/* ✅ SAFE IMAGE */}
//             {imageSrc ? (
//               <Image
//                 src={imageSrc}
//                 alt="Hero image"
//                 fill
//                 priority
//                 sizes="(max-width: 768px) 100vw, 50vw"
//                 className="object-contain transition duration-700 group-hover:scale-110"
//               />
//             ) : (
//               // fallback UI
//               <div className="w-full h-full flex items-center justify-center text-gray-400 text-sm">
//                 No image available
//               </div>
//             )}

//             {/* overlay */}
//             <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent" />
//           </div>
//         </div>
//       </div>
//     </section>
//   );
// }

"use client";

import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";
import Image from "next/image";

export default function HeroSection({ data }) {
  if (!data) return null;

  const [ref, visible] = useFadeIn();

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