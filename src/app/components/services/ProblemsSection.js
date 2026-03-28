export default function ProblemsSection({ problems = [] }) {
  return (
    <section className="py-24 bg-white">
      <div className="max-w-6xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            რა პრობლემებს ვაგვარებთ
          </h2>

          <p className="mt-4 text-gray-600 max-w-2xl mx-auto">
            რეალური გამოწვევები, რომლებიც აფერხებს შენს სისტემას და ზრდას
          </p>
        </div>

        {/* GRID */}
        <div className="grid md:grid-cols-3 gap-8">

          {problems.map((item, i) => (
            <div
              key={i}
              className="
                group
                bg-gray-50
                border border-gray-200
                rounded-2xl
                p-6
                transition-all duration-300
                hover:-translate-y-2
                hover:shadow-xl
              "
            >
              {/* ICON */}
              <div className="w-10 h-10 flex items-center justify-center rounded-xl bg-red-100 text-red-600 mb-4">
                !
              </div>

              {/* TEXT */}
              <p className="text-gray-700 leading-relaxed">
                {item.text}
              </p>

            </div>
          ))}

        </div>
      </div>
    </section>
  );
}