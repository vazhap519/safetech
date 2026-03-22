"use client";
import useFadeIn from "../../hooks/useFadeIn";

export default function WhyUs({data}) {
 if (!data) return null;
  const items = [
    {
      title: "სწრაფი მომსახურება",
      desc: "უმოკლეს დროში ვასრულებთ ყველა სამუშაოს.",
      icon: "⚡",
    },
    {
      title: "გამოცდილება",
      desc: "მრავალწლიანი გამოცდილება IT და უსაფრთხოების სფეროში.",
      icon: "🛠",
    },
    {
      title: "მაღალი ხარისხი",
      desc: "ვიყენებთ სანდო მოწყობილობებს და თანამედროვე ტექნოლოგიებს.",
      icon: "💯",
    },
    {
      title: "მთელი საქართველო",
      desc: "ვემსახურებით როგორც თბილისს, ასევე რეგიონებს.",
      icon: "📍",
    },
  ];
     const [ref, visible] = useFadeIn();

  return (
    <section
     ref={ref}
  className={`py-20 bg-[#0B3C5D] text-white text-center 
  transition-all duration-700 ease-out will-change-transform ${
    visible
      ? "opacity-100 translate-y-0"
      : "opacity-0 translate-y-10"
  }`}
    >
      <div className="max-w-7xl mx-auto px-4">

        {/* Title */}
        <div className="text-center">
        <h2 className="text-3xl font-bold text-white">
{data?.why_us_title}
</h2>
          <p className="text-gray-300 mt-2">
            {data?.why_us_description}
          </p>
        </div>

        {/* Grid */}
        <div className="grid md:grid-cols-4 gap-6 mt-12">

          {data?.why_us_items.map((item, i) => (
            <div
              key={i}
              className="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition duration-300 border border-gray-100"
            >

              {/* Icon */}
              <div className="w-12 h-12 flex items-center justify-center rounded-xl bg-[#00C2A8]/10 text-xl">
                {item.icon}
              </div>

              {/* Title */}
              <h3 className="mt-4 font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                {item.title}
              </h3>

              {/* Description */}
              <p className="text-sm text-gray-500 mt-2">
                {item.description}
              </p>

              {/* Hover line */}
              <div className="mt-4 h-[2px] w-0 bg-[#00C2A8] group-hover:w-full transition-all duration-300"></div>

            </div>
          ))}

        </div>

      </div>
    </section>
  );
}