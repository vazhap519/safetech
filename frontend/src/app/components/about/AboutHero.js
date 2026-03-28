// "use client";

// import Link from "next/link";
// import useFadeIn from "@/app/hooks/useFadeIn";
// import Image from "next/image";

// export default function AboutHero({Hero}) {
//   const [ref, visible] = useFadeIn();

//   return (
//     <section
//       ref={ref}
//       className={`relative bg-[#0B3C5D] text-white py-24 overflow-hidden transition-all duration-700 ${
//         visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
//       }`}
//     >
//       {/* 🔥 LIGHT GLOW */}
//       <div className="absolute -top-20 -left-20 w-72 h-72 bg-[#00C2A8]/20 blur-3xl rounded-full"></div>

//       {/* 🔥 GRADIENT OVERLAY */}
//       <div className="absolute inset-0 bg-gradient-to-r from-[#0B3C5D] via-[#0B3C5D]/90 to-transparent"></div>

//       <div className="relative max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">

//         {/* LEFT */}
//         <div>
//           <h1 className="text-4xl md:text-5xl font-bold leading-tight">
//           {Hero?.title}
//           </h1>

//         <div
//   className="mt-5 text-gray-300 text-lg leading-relaxed space-y-3 [&>p]:mb-2 [&>p]:leading-relaxed"
//   dangerouslySetInnerHTML={{
//     __html: Hero?.description || "",
//   }}
// />
//           {/* CTA */}
//           <div className="mt-8 flex gap-4 flex-wrap">

//             <Link
//               href="/services"
//               className="bg-[#00C2A8] px-6 py-3 rounded-xl  hover:bg-[#00a892] transition shadow-md hover:shadow-xl active:scale-95"
//             >
//               სერვისების ნახვა
//             </Link>

//             <Link
//               href="/contact"
//               className="border border-white/70 px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition active:scale-95"
//             >
//               დაგვიკავშირდი
//             </Link>

//           </div>

//           {/* TRUST */}
//           {/* <div className="mt-8 flex flex-wrap gap-5 text-sm text-gray-400">
//             {Hero.hero_trust_list.map(Key,item)=>{
//               return(
//           <span className="hover:text-white transition">✔ გამოცდილება</span>
//               )
//             }}
  
//             <span className="hover:text-white transition">✔ სწრაფი მომსახურება</span>
//             <span className="hover:text-white transition">✔ მთელი საქართველო</span>
//           </div> */}

//           <div className="mt-8 flex flex-wrap gap-5 text-sm text-gray-400">
//   {Hero?.trust_list ?.map((item, index) => (
//     <span
//       key={index}
//       className="flex items-center gap-2 hover:text-white transition"
//     >
//       <span className="text-[#00C2A8]">✔</span>
//     {item?.hero_trust_list_title || ""}
//   </span>
//   ))}
// </div>
//         </div>

//         {/* RIGHT */}
//       <div className="relative w-full h-[350px] md:h-[420px] group">
// <Image
//   src={Hero?.image || " "}
//   alt={
//     Hero?.title
//       ? `${Hero.title} - Safetech`
//       : "Safetech about section image"
//   }
//   fill
//   priority // 🔥 LCP fix (hero image)
//   fetchPriority="high" // 🔥 browser optimization
//   quality={60} // 🔥 smaller image size
//   sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 600px"
//   className="rounded-2xl object-cover transition duration-500 group-hover:scale-105"
// />

//   {/* overlay */}
//   <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent rounded-2xl"></div>

//   {/* badge */}
//   <div className="absolute bottom-5 left-5 bg-white text-[#0B3C5D] px-4 py-2 rounded-lg shadow-lg text-sm font-medium">
//     ⭐ {Hero?.badge}
//   </div>
// </div>

//       </div>
//     </section>
//   );
// }






"use client";

import Link from "next/link";
import useFadeIn from "@/app/hooks/useFadeIn";
import Image from "next/image";

export default function AboutHero({ Hero }) {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`
        relative 
        py-24 md:py-32
        bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B]
        text-white 
        overflow-hidden 
        transition-all duration-700
        ${visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"}
      `}
    >
      {/* 🔥 SOFT GLOW */}
      <div className="absolute -top-32 -left-32 w-[400px] h-[400px] bg-[#00E0B8]/20 blur-3xl rounded-full" />

      {/* 🔥 SECOND GLOW */}
      <div className="absolute bottom-0 right-0 w-[300px] h-[300px] bg-[#00C2FF]/10 blur-3xl rounded-full" />

      <div className="relative max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">

        {/* LEFT */}
        <div>

          {/* TITLE */}
          <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
            {Hero?.title}
          </h1>

          {/* DESCRIPTION */}
          <div
            className="
              mt-6 
              text-lg 
              text-white/80 
              leading-relaxed 
              max-w-xl
              space-y-3
              [&>p]:mb-2
            "
            dangerouslySetInnerHTML={{
              __html: Hero?.description || "",
            }}
          />

          {/* CTA */}
          <div className="mt-10 flex flex-wrap gap-4">

            <Link
              href="/services"
              className="
                bg-[#00E0B8]
                text-black
                px-8 py-4
                rounded-2xl
                font-semibold
                shadow-[0_10px_40px_rgba(0,224,184,0.35)]
                hover:scale-105
                transition-all duration-300
              "
            >
              🚀 სერვისების ნახვა
            </Link>

            <Link
              href="/contact"
              className="
                border border-white/20
                px-8 py-4
                rounded-2xl
                text-white/90
                hover:bg-white/10
                transition-all duration-300
              "
            >
              დაგვიკავშირდი
            </Link>

          </div>

          {/* TRUST */}
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

          {/* GLASS FRAME */}
          <div className="
            absolute inset-0
            rounded-3xl
            border border-white/10
            bg-white/5
            backdrop-blur-sm
          " />

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
            className="
              object-cover 
              rounded-3xl 
              shadow-2xl
            "
          />

          {/* OVERLAY */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-3xl" />

          {/* BADGE */}
          <div className="
            absolute bottom-6 left-6
            bg-white text-[#071A2B]
            px-5 py-2
            rounded-xl
            shadow-xl
            text-sm font-semibold
          ">
            ⭐ {Hero?.badge}
          </div>

        </div>

      </div>
    </section>
  );
}