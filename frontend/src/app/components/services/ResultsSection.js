export default function ResultsSection({ results = [] }) {
  return (
    <section className="py-24 bg-gradient-to-b from-green-50 to-white">
      <div className="max-w-5xl mx-auto text-center px-6">

        {/* HEADER */}
        <div className="mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            შედეგები
          </h2>

          <p className="mt-4 text-gray-600">
            რეალური მონაცემები ჩვენი სისტემის ეფექტურობაზე
          </p>
        </div>

        {/* KPI GRID */}
        <div className="grid md:grid-cols-3 gap-8">

          {results.map((item, i) => (
            <div
              key={i}
              className="
                group
                bg-white
                border border-gray-200
                rounded-2xl
                p-8
                transition-all duration-300
                hover:-translate-y-2
                hover:shadow-xl
              "
            >
              {/* NUMBER */}
              <p className="
                text-3xl md:text-4xl 
                font-bold 
                text-green-600
                mb-2
              ">
                {item?.text || item}
              </p>

              {/* LABEL */}
              <p className="text-sm text-gray-500">
                performance improvement
              </p>

            </div>
          ))}

        </div>

      </div>
    </section>
  );
}