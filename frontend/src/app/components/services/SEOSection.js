export default function SEOSection({ title, paragraphs }) {
  if (!paragraphs || paragraphs.length === 0) return null;

  return (
    <section className="py-20 bg-white">
      <div className="max-w-4xl mx-auto px-4 text-center">

        <h2 className="text-3xl font-bold text-[#0B3C5D]">
          {title}
        </h2>

        <div className="mt-6 space-y-4 text-gray-600 leading-relaxed">
          {paragraphs.map((text, i) => (
            <p key={i}>{text}</p>
          ))}
        </div>

      </div>
    </section>
  );
}