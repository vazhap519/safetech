export default function FeaturesSection({ features, title = "რას გთავაზობთ" }) {
  if (!features || features.length === 0) return null;

  return (
    <section className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">

        <h2 className="text-3xl font-bold text-[#0B3C5D] text-center">
          {title}
        </h2>

        <div className="grid md:grid-cols-2 gap-6 mt-10">

          {features.map((item, i) => (
            <div
              key={i}
              className="bg-white p-5 rounded-xl shadow-sm flex items-start gap-3 hover:shadow-md transition"
            >
              <span className="text-[#00C2A8] text-xl">✔</span>
              <p className="text-gray-700 leading-relaxed">{item}</p>
            </div>
          ))}

        </div>

      </div>
    </section>
  );
}