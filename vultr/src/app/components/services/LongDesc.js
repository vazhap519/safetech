export default function LongDesc({ long }) {
  if (!long) return null;

  const paragraphs = long.split("\n").filter(p => p.trim() !== "");

  return (
    <section className="py-24 bg-white">
      <div className="max-w-3xl mx-auto px-6">

        {/* CONTENT */}
        <div className="
          bg-gray-50
          border border-gray-200
          rounded-3xl
          p-8 md:p-12
        ">

          <div className="
            space-y-6
            text-gray-700
            text-lg
            leading-relaxed
          ">
            {paragraphs.map((paragraph, i) => (
              <p
                key={i}
                className="
                  first:text-xl 
                  first:font-medium 
                  first:text-gray-900
                "
              >
                {paragraph}
              </p>
            ))}
          </div>

        </div>

      </div>
    </section>
  );
}