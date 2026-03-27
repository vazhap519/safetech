export default function LongDesc({ long }) {
  if (!long) return null;

  const paragraphs = long.split("\n").filter(p => p.trim() !== "");

  return (
    <section className="py-16 bg-[#F8FAFC]">
      <div className="max-w-4xl mx-auto px-4">

        <div className="bg-white p-6 md:p-10 rounded-2xl shadow-sm">

          <div className="space-y-4 text-gray-700 text-lg leading-relaxed">
            {paragraphs.map((paragraph, i) => (
              <p key={i}>
                {paragraph}
              </p>
            ))}
          </div>

        </div>

      </div>
    </section>
  );
}