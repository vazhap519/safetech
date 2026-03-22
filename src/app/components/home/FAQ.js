"use client";

import { useState } from "react";
import useFadeIn from "../../hooks/useFadeIn";
 
export default function FAQ() {
  const [openIndex, setOpenIndex] = useState(null);
   const [ref, visible] = useFadeIn();

  const faqs = [
    {
      question: "რამდენ ხანში ხდება მონტაჟი?",
      answer: "უმეტეს შემთხვევაში სამუშაო სრულდება 1-2 დღეში, დამოკიდებულია პროექტის სირთულეზე.",
    },
    {
      question: "რა ღირს სერვისები?",
      answer: "ფასი დამოკიდებულია სამუშაოს მოცულობაზე. დაგვიკავშირდი და მოგცემთ ზუსტ შეფასებას.",
    },
    {
      question: "რომელ ქალაქებში მუშაობთ?",
      answer: "ვმუშაობთ მთელ საქართველოში — თბილისი და რეგიონები.",
    },
    {
      question: "რა ტიპის კამერებს აყენებთ?",
      answer: "ვაყენებთ თანამედროვე IP და ანალოგურ კამერებს მაღალი ხარისხით.",
    },
    {
      question: "გთავაზობთ თუ არა კონსულტაციას?",
      answer: "დიახ, კონსულტაცია უფასოა.",
    },
  ];

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
            ხშირად დასმული კითხვები
          </h2>
          <p className="text-gray-500 mt-2">
            პასუხები ყველაზე გავრცელებულ კითხვებზე
          </p>
        </div>

        {/* FAQ List */}
        <div className="mt-10 space-y-4">

          {faqs.map((faq, i) => (
            <div
              key={i}
              className="bg-white rounded-xl shadow-sm border border-gray-100"
            >

              {/* Question */}
              <button
                onClick={() => setOpenIndex(openIndex === i ? null : i)}
                className="w-full flex justify-between items-center p-4 text-left font-medium text-[#0B3C5D]"
              >
                {faq.question}
                <span>
                  {openIndex === i ? "−" : "+"}
                </span>
              </button>

              {/* Answer */}
              {openIndex === i && (
                <div className="px-4 pb-4 text-sm text-gray-500">
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