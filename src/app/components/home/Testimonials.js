// "use client";

// import { useEffect, useRef } from "react";
// import useFadeIn from "../../hooks/useFadeIn";

// export default function Testimonials({ items = [] }) {
//   const scrollRef = useRef(null);
//   const [ref, visible] = useFadeIn();

//   // 🔥 AUTO SCROLL
//   useEffect(() => {
//     const container = scrollRef.current;
//     if (!container) return;

//     let scrollAmount = 0;

//     const interval = setInterval(() => {
//       scrollAmount += 1;
//       container.scrollLeft = scrollAmount;

//       // loop
//       if (scrollAmount >= container.scrollWidth / 2) {
//         scrollAmount = 0;
//       }
//     }, 20);

//     return () => clearInterval(interval);
//   }, []);

//   return (
//     <section
//       ref={ref}
//       className={`relative py-28 bg-gradient-to-b from-gray-50 to-white transition-all duration-700 ${
//         visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
//       }`}
//     >
//       <div className="max-w-6xl mx-auto px-4 text-center">

//         {/* TITLE */}
//         <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-2">
//           შეფასებები
//         </p>

//         <h2 className="text-2xl md:text-4xl font-bold text-[#0B3C5D] mb-14">
//           რას ამბობენ კლიენტები
//         </h2>

//         {/* CAROUSEL */}
//         <div className="relative">

//           {/* fade edges */}
//           <div className="absolute left-0 top-0 w-24 h-full bg-gradient-to-r from-white to-transparent z-10"></div>
//           <div className="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-white to-transparent z-10"></div>

//           <div
//             ref={scrollRef}
//             className="flex gap-6 overflow-hidden px-2"
//           >

//             {[...items, ...items].map((item, i) => (
//               <div
//                 key={i}
//                 className={`min-w-[280px] md:min-w-[320px] relative bg-white rounded-3xl p-6 
//                 border border-gray-100 shadow-sm 
//                 hover:shadow-2xl hover:-translate-y-2 
//                 transition-all duration-700 text-left group
//                 ${
//                   visible
//                     ? "opacity-100 translate-y-0"
//                     : "opacity-0 translate-y-10"
//                 }`}
//                 style={{
//                   transitionDelay: `${i * 100}ms`,
//                 }}
//               >

//                 {/* 🔥 glow */}
//                 <div className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 
//                 bg-[#00C2A8]/5 blur-xl transition duration-500"></div>

//                 {/* quote */}
//                 <p className="text-gray-600 leading-relaxed text-sm relative z-10">
//                   “{item.text}”
//                 </p>

//                 {/* divider */}
//                 <div className="w-10 h-[2px] bg-gradient-to-r from-[#00C2A8] to-transparent mt-5"></div>

//                 {/* user */}
//                 <div className="mt-5 flex items-center gap-3">

//                   {/* avatar */}
//                   <div className="w-10 h-10 rounded-full bg-[#00C2A8]/20 flex items-center justify-center text-[#00C2A8] font-bold">
//                     {item.name?.charAt(0)}
//                   </div>

//                   <p className="font-semibold text-[#0B3C5D]">
//                     {item.name}
//                   </p>

//                 </div>

//               </div>
//             ))}

//           </div>
//         </div>

//       </div>
//     </section>
//   );
// }

"use client";

import { useEffect, useRef } from "react";
import useFadeIn from "../../hooks/useFadeIn";

export default function Testimonials({ items = [] }) {
  const scrollRef = useRef(null);
  const [ref, visible] = useFadeIn();

  // 🔥 AUTO SCROLL
  useEffect(() => {
    const container = scrollRef.current;
    if (!container) return;

    let scrollAmount = 0;

    const interval = setInterval(() => {
      scrollAmount += 1;
      container.scrollLeft = scrollAmount;

      if (scrollAmount >= container.scrollWidth / 2) {
        scrollAmount = 0;
      }
    }, 20);

    return () => clearInterval(interval);
  }, []);

  return (
    <section
      ref={ref}
      className={`relative py-28 bg-gradient-to-b from-gray-50 to-white transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-6xl mx-auto px-4 text-center">

        <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-2">
          შეფასებები
        </p>

        <h2 className="text-2xl md:text-4xl font-bold text-[#0B3C5D] mb-14">
          რას ამბობენ კლიენტები
        </h2>

        <div className="relative">

          <div className="absolute left-0 top-0 w-24 h-full bg-gradient-to-r from-white to-transparent z-10"></div>
          <div className="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-white to-transparent z-10"></div>

          <div
            ref={scrollRef}
            className="flex gap-6 overflow-hidden px-2"
          >

            {[...items, ...items].map((item, i) => (
              <div
                key={i}
                className={`min-w-[280px] md:min-w-[320px] relative bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-700 text-left group ${
                  visible
                    ? "opacity-100 translate-y-0"
                    : "opacity-0 translate-y-10"
                }`}
                style={{
                  transitionDelay: `${i * 100}ms`,
                }}
              >

                {/* 🔥 glow */}
                <div className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 bg-[#00C2A8]/5 blur-xl transition duration-500"></div>

                <p className="text-gray-600 leading-relaxed text-sm relative z-10">
                  “{item.text}”
                </p>

                <div className="w-10 h-[2px] bg-gradient-to-r from-[#00C2A8] to-transparent mt-5"></div>

                <div className="mt-5 flex items-center gap-3">

                  <div className="w-10 h-10 rounded-full bg-[#00C2A8]/20 flex items-center justify-center text-[#00C2A8] font-bold">
                    {item.name?.charAt(0)}
                  </div>

                  <p className="font-semibold text-[#0B3C5D]">
                    {item.name}
                  </p>

                </div>

              </div>
            ))}

          </div>
        </div>

      </div>
    </section>
  );
}