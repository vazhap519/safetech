// import Image from "next/image";
// export default function ServiceHero({ service }) {
//   if (!service) return null;

//   return (
//     <section className="bg-[#0B3C5D] text-white py-20">
//       <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

//         <div>
//           <h1 className="text-4xl md:text-5xl font-bold">
//             {service.title}
//           </h1>

//           <p className="mt-4 text-gray-300 text-lg">
//             {service.description}
//           </p>

//           <a
//             href={`tel:${service.phone || "+995599000000"}`}
//             className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl"
//           >
//             {service.button_text || "📞 დაგვირეკე"}
//           </a>
//         </div>

//         <div className="relative w-full h-[300px] md:h-[400px]">
//           <Image
//             src={service.image || "/placeholder.jpg"}
//             alt={service.title}
//             fill
//             className="object-cover rounded-2xl"
//             priority
//           />
//         </div>

//       </div>
//     </section>
//   );
// }

// import Image from "next/image";

// export default function ServiceHero({ service }) {
//   if (!service) return null;

//   return (
//  <section className="
//   relative 
//   py-24 md:py-32 
//   bg-gradient-to-br 
//   from-[#071A2B] 
//   via-[#0A2238] 
//   to-[#071A2B]
//   text-white
//   overflow-hidden
// ">

//       {/* 🔥 BACKGROUND GLOW */}
//       <div className="absolute inset-0 bg-hero-gradient opacity-30 pointer-events-none" />

//       <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 md:gap-16 items-center">

//         {/* 🧠 TEXT SIDE */}
//         <div className="relative z-10">

//           <h1 className="text-4xl md:text-6xl font-bold text-white">
//             {service.title}
//           </h1>

//           <p className="text-lg text-white/80">
//             {service.description}
//           </p>

//           {/* CTA */}
//           <div className="flex flex-wrap gap-4 mt-10">

//             <a
//               href={`tel:${service.phone || "+995599000000"}`}
//               className="
//                 bg-accent 
//                 hover:bg-accent-hover 
//                 text-black 
//                 px-8 py-4 
//                 rounded-2xl 
//                 font-semibold 
//                 shadow-glow 
//                 transition-all duration-300 
//                 hover:scale-105
//               "
//             >
//               📞 {service.button_text || "დაგვიკავშირდი"}
//             </a>

//             <button
//               className="
//                 border border-dark-border 
//                 text-text-primary 
//                 px-6 py-4 
//                 rounded-2xl 
//                 hover:bg-glass 
//                 transition-all duration-300
//               "
//             >
//               დეტალურად
//             </button>

//           </div>

//         </div>

//         {/* 🖼 IMAGE SIDE */}
//         <div className="relative h-[320px] md:h-[450px]">

//           <div className="
//             absolute inset-0 
//             rounded-3xl 
//             bg-glass 
//             backdrop-blur-xl 
//             border border-dark-border
//           " />

//         <div className="relative h-[320px] md:h-[450px]">

//   <Image
//     src={service.image || "/placeholder.jpg"}
//     alt={service.title}
//     fill
//     className="object-cover rounded-3xl shadow-2xl"
//   />

// </div>

//         </div>

//       </div>
//     </section>
//   );
// }


import Image from "next/image";

export default function ServiceHero({ service }) {
  if (!service) return null;

  return (
    <section className="
      relative py-24 md:py-32 
      bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B]
      text-white overflow-hidden
    ">

      {/* DARK OVERLAY */}
      <div className="absolute inset-0 bg-black/40 z-0" />

      <div className="relative z-10 max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">

        <div>
          <h1 className="text-4xl md:text-6xl font-bold leading-tight">
            {service.title}
          </h1>

<p className="mt-6 text-lg text-white/90 max-w-xl">
            {service.description}
          </p>

          <a
            href={`tel:${service.phone || "+995599000000"}`}
            className="
              inline-block mt-8 
              bg-[#00E0B8] 
              text-black 
              px-8 py-4 
              rounded-2xl 
              font-semibold 
              shadow-xl 
              hover:scale-105 
              transition
            "
          >
            📞 {service.button_text || "დაგვიკავშირდი"}
          </a>
        </div>

        <div className="relative h-[320px] md:h-[450px]">
          <Image
            src={service.image || "/placeholder.jpg"}
            alt={service.title}
            fill
            className="object-cover rounded-3xl shadow-2xl"
            priority
          />
        </div>

      </div>
    </section>
  );
}