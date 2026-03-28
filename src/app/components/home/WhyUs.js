// "use client";
// import useFadeIn from "../../hooks/useFadeIn";
// import {
//   FaBolt,
//   FaTools,
//   FaShieldAlt,
//   FaStar,
//   FaMapMarkerAlt,
//   FaHeadset,
//   FaClock,
//   FaRocket,
//   FaCogs,
//   FaCode,
//   FaChartLine,
//   FaUsers,
//   FaLock,
//   FaCheckCircle,
//   FaLightbulb,
//   FaCloud,
//   FaDatabase,
// } from "react-icons/fa";
// const iconMap = {
//   zap: FaBolt,
//   tools: FaTools,
//   shield: FaShieldAlt,
//   star: FaStar,
//   map: FaMapMarkerAlt,
//   support: FaHeadset,
//   clock: FaClock,
//   rocket: FaRocket,

//   settings: FaCogs,
//   code: FaCode,
//   chart: FaChartLine,
//   team: FaUsers,
//   lock: FaLock,
//   verified: FaCheckCircle,
//   idea: FaLightbulb,
//   cloud: FaCloud,
//   data: FaDatabase,
// };
// export default function WhyUs({ data }) {
//   if (!data) return null;

//   const [ref, visible] = useFadeIn();

//   return (
//     <section
//   ref={ref}
//   className={`py-24 bg-[#071E2F] text-white transition-all duration-700 ${
//     visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
//   }`}
// >
//   <div className="max-w-7xl mx-auto px-6">

//     <div className="text-center max-w-2xl mx-auto">
//       <h2 className="text-4xl font-bold">{data.title}</h2>
//       <p className="text-gray-400 mt-3">{data.description}</p>
//     </div>

//     <div className="grid md:grid-cols-4 gap-6 mt-16">
//       {data.items.map((item, i) => (
//         <div
//           key={i}
//           className="group p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl hover:bg-white/10 transition-all hover:-translate-y-2 hover:shadow-2xl"
//         >
//           <div className="w-12 h-12 flex items-center justify-center rounded-xl bg-[#00C2A8]/20 text-xl">
//             {(() => {
//     const Icon = iconMap[item.icon];
//     return Icon ? <Icon size={22} /> : <FaBolt />;
//   })()}
//           </div>

//           <h3 className="mt-4 font-semibold text-white group-hover:text-[#00C2A8]">
//             {item.title}
//           </h3>

//           <p className="text-sm text-gray-400 mt-2">
//             {item.description}
//           </p>

//           <div className="mt-4 h-[2px] w-0 bg-[#00C2A8] group-hover:w-full transition-all"></div>
//         </div>
//       ))}
//     </div>

//   </div>
// </section>
//   );
// }



"use client";
import useFadeIn from "../../hooks/useFadeIn";
import {
  FaBolt, FaTools, FaShieldAlt, FaStar, FaMapMarkerAlt,
  FaHeadset, FaClock, FaRocket, FaCogs, FaCode,
  FaChartLine, FaUsers, FaLock, FaCheckCircle,
  FaLightbulb, FaCloud, FaDatabase,
} from "react-icons/fa";

const iconMap = {
  zap: FaBolt,
  tools: FaTools,
  shield: FaShieldAlt,
  star: FaStar,
  map: FaMapMarkerAlt,
  support: FaHeadset,
  clock: FaClock,
  rocket: FaRocket,
  settings: FaCogs,
  code: FaCode,
  chart: FaChartLine,
  team: FaUsers,
  lock: FaLock,
  verified: FaCheckCircle,
  idea: FaLightbulb,
  cloud: FaCloud,
  data: FaDatabase,
};

export default function WhyUs({ data }) {
  if (!data) return null;

  const [ref, visible] = useFadeIn();

  return (
  <section
  ref={ref}
  style={{
    transform: visible ? "translateY(0px)" : "translateY(40px)",
  }}
  className="py-28 bg-gradient-to-b from-[#071A2B] via-[#071E2F] to-[#071A2B] text-white transition-all duration-700"
>
      <div className="max-w-7xl mx-auto px-6">

        {/* TITLE */}
        <div className="text-center max-w-2xl mx-auto">
          <p className="text-[#00C2A8] text-sm uppercase tracking-widest mb-2">
            რატომ ჩვენ
          </p>

          <h2 className="text-4xl md:text-5xl font-bold">
            {data.title}
          </h2>

          <p className="text-gray-400 mt-4 leading-relaxed">
            {data.description}
          </p>
        </div>

        {/* CARDS */}
        <div className="grid md:grid-cols-4 gap-6 mt-20">
          {data.items.map((item, i) => {
            const Icon = iconMap[item.icon] || FaBolt;

            return (
              <div
                key={i}
                className={`group relative p-6 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xl hover:border-[#00C2A8]/40 hover:shadow-[0_0_30px_rgba(0,194,168,0.2)] transition-all duration-700 hover:-translate-y-2
      ${
        visible
          ? "opacity-100 translate-y-0"
          : "opacity-0 translate-y-10"
      }`}
      style={{
        transitionDelay: `${i * 120}ms`,
      }}
              >

                {/* ICON */}
                {/* <div className="w-14 h-14 flex items-center justify-center rounded-xl  bg-gradient-to-br from-[#00C2A8]/20 to-[#00C2FF]/20 text-[#00C2A8] text-2xl group-hover:scale-110 transition duration-300 relative overflow-hidden">
                  <Icon />
                </div> */}

<div className="w-14 h-14 flex items-center justify-center rounded-xl 
bg-gradient-to-br from-[#00C2A8]/20 to-[#00C2FF]/20
text-[#00C2A8] text-2xl
group-hover:scale-110 transition duration-300 relative overflow-hidden">

  <Icon className="relative z-10" />

  {/* shimmer */}
  <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition">
    <div className="w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-[shine_1.5s_linear]"></div>
  </div>

</div>



                {/* TITLE */}
                <h3 className="mt-5 font-semibold text-white text-lg group-hover:text-[#00C2A8] transition">
                  {item.title}
                </h3>

                {/* DESCRIPTION */}
                <p className="text-sm text-gray-400 mt-3 leading-relaxed line-clamp-4">
                  {item.description}
                </p>

                {/* LINE ANIMATION */}
                <div className="mt-5 h-[2px] w-0 bg-gradient-to-r from-[#00C2A8] to-transparent 
                group-hover:w-full transition-all duration-500"></div>

                {/* GLOW EFFECT */}
                <div className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 
                bg-[#00C2A8]/5 blur-xl transition duration-500"></div>
              </div>
            );
          })}
        </div>

      </div>
    </section>
  );
}