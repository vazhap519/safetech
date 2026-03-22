"use client";

import useFadeIn from "@/app/hooks/useFadeIn";

const items = [
  {
    title: "სწრაფი რეაგირება",
    desc: "მოთხოვნაზე რეაგირება უმოკლეს დროში, ხშირად იმავე დღეს.",
    icon: "⚡",
  },
  {
    title: "გამოცდილი გუნდი",
    desc: "პროფესიონალები მრავალწლიანი გამოცდილებით IT სფეროში.",
    icon: "🛠",
  },
  {
    title: "თანამედროვე ტექნოლოგიები",
    desc: "ვიყენებთ მხოლოდ სანდო და თანამედროვე მოწყობილობებს.",
    icon: "💻",
  },
  {
    title: "მთელი საქართველო",
    desc: "ვემსახურებით როგორც თბილისს, ასევე რეგიონებს.",
    icon: "📍",
  },
  {
    title: "ინდივიდუალური მიდგომა",
    desc: "ყველა პროექტი მორგებულია კონკრეტულ საჭიროებებზე.",
    icon: "🎯",
  },
  {
    title: "მხარდაჭერა 24/7",
    desc: "ნებისმიერ დროს მზად ვართ დახმარებისთვის.",
    icon: "📞",
  },
];

export default function AboutWhyUs() {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`py-20 bg-white transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-7xl mx-auto px-4">

        {/* TITLE */}
        <div className="text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-[#0B3C5D]">
            რატომ Safetech?
          </h2>

          <p className="mt-4 text-gray-600 max-w-xl mx-auto">
            ჩვენ ვქმნით საიმედო და ეფექტურ IT გადაწყვეტილებებს, რომლებიც რეალურად მუშაობს.
          </p>
        </div>

        {/* GRID */}
        <div className="mt-12 grid md:grid-cols-3 gap-6">

          {items.map((item, i) => (
            <div
              key={i}
              className={`group bg-[#F8FAFC] p-6 rounded-2xl shadow-sm 
              hover:shadow-xl hover:-translate-y-1 transition-all duration-500
              ${
                visible
                  ? "opacity-100 translate-y-0"
                  : "opacity-0 translate-y-10"
              }`}
              style={{ transitionDelay: `${i * 120}ms` }}
            >

              {/* ICON */}
              <div className="text-3xl">
                {item.icon}
              </div>

              {/* TITLE */}
              <h3 className="mt-4 font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                {item.title}
              </h3>

              {/* DESC */}
              <p className="text-sm text-gray-600 mt-2 leading-relaxed">
                {item.desc}
              </p>

            </div>
          ))}

        </div>

      </div>
    </section>
  );
}