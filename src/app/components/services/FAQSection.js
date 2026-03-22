"use client";

import { useState } from "react";

export default function FAQSection({ faq, title = "ხშირად დასმული კითხვები" }) {
  const [openIndex, setOpenIndex] = useState(null);

  if (!faq || faq.length === 0) return null;

  return (
    <section className="py-20 bg-[#F8FAFC]">
      <div className="max-w-4xl mx-auto px-4">

        <h2 className="text-3xl font-bold text-[#0B3C5D] text-center">
          {title}
        </h2>

        <div className="mt-10 space-y-4">

          {faq.map((item, i) => (
            <div
              key={i}
              className="bg-white rounded-xl shadow-sm overflow-hidden"
            >
              {/* QUESTION */}
              <button
                onClick={() =>
                  setOpenIndex(openIndex === i ? null : i)
                }
                className="w-full text-left p-5 flex justify-between items-center"
              >
                <span className="font-semibold text-[#0B3C5D]">
                  {item.q}
                </span>

                <span className="text-[#00C2A8] text-xl">
                  {openIndex === i ? "−" : "+"}
                </span>
              </button>

              {/* ANSWER */}
              <div
                className={`px-5 overflow-hidden transition-all duration-300 ${
                  openIndex === i ? "max-h-40 pb-5" : "max-h-0"
                }`}
              >
                <p className="text-gray-600">
                  {item.a}
                </p>
              </div>
            </div>
          ))}

        </div>

      </div>
    </section>
  );
}