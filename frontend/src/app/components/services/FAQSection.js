"use client";

import { useState } from "react";

export default function FAQSection({ faq, title = "ხშირად დასმული კითხვები" }) {
  const [openIndex, setOpenIndex] = useState(null);

  if (!faq || faq.length === 0) return null;

  return (
    <section className="py-24 bg-gray-50">
      <div className="max-w-4xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            {title}
          </h2>

          <p className="mt-4 text-gray-600">
            პასუხები ყველაზე ხშირად დასმულ კითხვებზე
          </p>
        </div>

        {/* FAQ LIST */}
        <div className="space-y-4">

          {faq.map((item, i) => {
            const isOpen = openIndex === i;

            return (
              <div
                key={i}
                className={`
                  border border-gray-200
                  rounded-2xl
                  bg-white
                  transition-all duration-300
                  ${isOpen ? "shadow-lg" : ""}
                `}
              >

                {/* QUESTION */}
                <button
                  onClick={() =>
                    setOpenIndex(isOpen ? null : i)
                  }
                  className="w-full flex justify-between items-center p-6 text-left"
                >
                  <span className="font-semibold text-gray-900">
                    {item.q}
                  </span>

                  <span className={`
                    text-xl
                    transition-transform duration-300
                    ${isOpen ? "rotate-45 text-green-600" : "text-gray-400"}
                  `}>
                    +
                  </span>
                </button>

                {/* ANSWER */}
                <div
                  className={`
                    grid transition-all duration-300 ease-in-out
                    ${isOpen ? "grid-rows-[1fr] opacity-100" : "grid-rows-[0fr] opacity-0"}
                  `}
                >
                  <div className="overflow-hidden">
                    <p className="px-6 pb-6 text-gray-600 leading-relaxed">
                      {item.a}
                    </p>
                  </div>
                </div>

              </div>
            );
          })}

        </div>

      </div>
    </section>
  );
}