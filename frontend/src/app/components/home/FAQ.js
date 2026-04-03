"use client";

import { useState } from "react";
import useFadeIn from "../../hooks/useFadeIn";
 
export default function FAQ({data}) {
   if (!data) return null;
  const [openIndex, setOpenIndex] = useState(null);
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
      <div className="max-w-4xl mx-auto px-4">

        {/* Title */}
        <div className="text-center">
          <h2 className="text-3xl font-bold text-white">
           {data.title}
          </h2>
          <p className="text-gray-500 mt-2">
           {data.description}
          </p>
        </div>

        {/* FAQ List */}
     
<div className="mt-12 space-y-4">
  {data.faq.map((faq, i) => (
    <div
      key={i}
      className="bg-white/5 border border-white/10 backdrop-blur-xl rounded-xl overflow-hidden"
    >
      <button
        onClick={() => setOpenIndex(openIndex === i ? null : i)}
        className="w-full flex justify-between items-center p-5 text-left text-white"
      >
        {faq.question}
        <span className="text-[#00C2A8] text-xl">
          {openIndex === i ? "−" : "+"}
        </span>
      </button>

      {openIndex === i && (
        <div className="px-5 pb-5 text-gray-300 text-sm">
          {faq.answer}
        </div>
      )}
    </div>
  ))}
</div>
      </div>
    </section>
  );
}