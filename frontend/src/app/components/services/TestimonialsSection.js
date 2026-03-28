export default function TestimonialsSection({ items = [] }) {
  return (
    <section className="py-24 bg-gray-50">
      <div className="max-w-6xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            რას ამბობენ მომხმარებლები
          </h2>

          <p className="mt-4 text-gray-600">
            ჩვენი კლიენტების რეალური გამოცდილება
          </p>
        </div>

        {/* GRID */}
        <div className="grid md:grid-cols-3 gap-8">

          {items.map((item, i) => (
            <div
              key={i}
              className="
                group
                bg-white
                border border-gray-200
                rounded-2xl
                p-6
                transition-all duration-300
                hover:-translate-y-2
                hover:shadow-xl
              "
            >
              {/* QUOTE ICON */}
              <div className="text-3xl text-gray-300 mb-4">“</div>

              {/* TEXT */}
              <p className="text-gray-700 leading-relaxed mb-6">
                {item.text}
              </p>

              {/* USER */}
              <div className="flex items-center gap-3">

                {/* AVATAR */}
                <div className="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-semibold">
                  {item.name?.charAt(0)}
                </div>

                {/* INFO */}
                <div>
                  <p className="text-sm font-semibold text-gray-900">
                    {item.name}
                  </p>
                  <p className="text-xs text-gray-500">
                    მომხმარებელი
                  </p>
                </div>

              </div>

            </div>
          ))}

        </div>
      </div>
    </section>
  );
}