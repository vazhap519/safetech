export default function FeaturesSection({ features, title = "რას გთავაზობთ" }) {
  if (!features || features.length === 0) return null;

  return (
    <section className="py-24 bg-gray-50">
      <div className="max-w-6xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            {title}
          </h2>

          <p className="mt-4 text-gray-600 max-w-2xl mx-auto">
            ეფექტური გადაწყვეტილებები, რომლებიც რეალურად მუშაობს
          </p>
        </div>

        {/* GRID */}
        <div className="grid md:grid-cols-2 gap-8">

          {features.map((item, i) => (
            <div
              key={i}
              className="
                group
                bg-white
                border border-gray-200
                rounded-2xl
                p-6
                flex items-start gap-4
                transition-all duration-300
                hover:-translate-y-2
                hover:shadow-xl
              "
            >
              {/* ICON */}
              <div className="
                w-10 h-10 flex items-center justify-center
                rounded-xl
                bg-green-100
                text-green-600
                flex-shrink-0
              ">
                ✓
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