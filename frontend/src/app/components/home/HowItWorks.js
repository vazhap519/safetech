"use client";
import useFadeIn from "../../hooks/useFadeIn";

export default function HowItWorks({data}) {
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
  }`}>
      <div className="max-w-7xl mx-auto px-4">

        {/* Title */}
        <div className="text-center">
          <h2 className="text-3xl font-bold text-white">
          {data.title}
          </h2>
          <p className="text-gray-300 mt-2">
            {data.description}
          </p>
        </div>

        {/* Steps */}
        <div className="grid md:grid-cols-3 gap-8 mt-12 relative">

          {data.cards.map((step, i) => (
            <div
              key={i}
              className="relative bg-[#F8FAFC] p-6 rounded-2xl text-center shadow-sm hover:shadow-lg transition"
            >

              {/* Number */}
              <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#00C2A8] text-white w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold">
                {i + 1}
              </div>

              {/* Icon */}
              <div className="text-3xl mt-4">
                {step.icon}
              </div>

              {/* Title */}
              <h3 className="mt-4 font-semibold text-[#0B3C5D]">
                {step.title}
              </h3>

              {/* Description */}
              <p className="text-sm text-gray-500 mt-2">
                {step.desc}
              </p>
            </div>
          ))}

        </div>

      </div>
    </section>
  );
}