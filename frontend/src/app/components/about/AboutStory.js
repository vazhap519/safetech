"use client";

import useFadeIn from "@/app/hooks/useFadeIn";

export default function AboutStory() {
  const [ref, visible] = useFadeIn();

  return (
    <section
      ref={ref}
      className={`py-20 bg-[#F8FAFC] transition-all duration-700 ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
      }`}
    >
      <div className="max-w-6xl mx-auto px-4">

        {/* TITLE */}
        <div className="text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-[#0B3C5D]">
            ჩვენს შესახებ
          </h2>

          <p className="mt-4 text-gray-600 max-w-2xl mx-auto">
            Safetech არის IT სერვისების კომპანია, რომელიც უზრუნველყოფს უსაფრთხოების სისტემებს, 
            ქსელურ ინფრასტრუქტურას და ტექნიკურ მხარდაჭერას საქართველოში.
          </p>
        </div>

        {/* CONTENT */}
        <div className="mt-12 grid md:grid-cols-2 gap-10 items-center">

          {/* TEXT */}
          <div className="space-y-4 text-gray-700 leading-relaxed">

            <p>
              ჩვენი მიზანია მომხმარებლებს შევთავაზოთ თანამედროვე და სანდო ტექნოლოგიური გადაწყვეტილებები. 
              ჩვენ ვმუშაობთ როგორც კერძო პირებთან, ასევე ბიზნესებთან და ვქმნით სრულად მორგებულ სისტემებს.
            </p>

            <p>
              Safetech სპეციალიზდება კამერების მონტაჟში, POS სისტემებში, ინტერნეტის გაყვანაში, 
              როუტერების კონფიგურაციაში და ოპერაციული სისტემების ინსტალაციაში.
            </p>

            <p>
              ჩვენი გუნდი მუდმივად იყენებს თანამედროვე ტექნოლოგიებს და უზრუნველყოფს სწრაფ, 
              ხარისხიან და უსაფრთხო მომსახურებას მთელი საქართველოს მასშტაბით.
            </p>

          </div>

          {/* STATS */}
          <div className="grid grid-cols-2 gap-6">

            {[
              { value: "100+", label: "კმაყოფილი კლიენტი" },
              { value: "5+", label: "წლიანი გამოცდილება" },
              { value: "24/7", label: "მხარდაჭერა" },
              { value: "100%", label: "ხარისხის გარანტია" },
            ].map((item, i) => (
              <div
                key={i}
                className={`bg-white p-6 rounded-xl shadow text-center 
                transition-all duration-500 hover:-translate-y-1 hover:shadow-lg
                ${
                  visible
                    ? "opacity-100 translate-y-0"
                    : "opacity-0 translate-y-10"
                }`}
                style={{ transitionDelay: `${i * 120}ms` }}
              >
                <h3 className="text-2xl font-bold text-[#00C2A8]">
                  {item.value}
                </h3>

                <p className="text-sm text-gray-600 mt-1">
                  {item.label}
                </p>
              </div>
            ))}

          </div>

        </div>

      </div>
    </section>
  );
}