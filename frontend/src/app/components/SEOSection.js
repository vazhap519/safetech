import { injectInternalLinks } from "@/lib/internalLinks";

export default function SEOSection({
  title,
  paragraphs = [],
  links = [],
}) {
  if (!Array.isArray(paragraphs) || paragraphs.length === 0) return null;

  return (
    <section className="py-20 bg-white">
      <div className="max-w-4xl mx-auto px-4">

        {title && (
          <h2 className="text-3xl font-bold text-[#0B3C5D] text-center">
            {title}
          </h2>
        )}

        <div className="mt-6 space-y-4 text-gray-600 leading-relaxed">

          {paragraphs.map((text, i) => {
            const html = injectInternalLinks(text, links);

            return (
              <p
                key={i}
                dangerouslySetInnerHTML={{ __html: html }}
              />
            );
          })}

        </div>

      </div>
    </section>
  );
}