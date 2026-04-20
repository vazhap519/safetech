import { injectInternalLinks } from "@/lib/internalLinks";

export default function SEOCategoryBlock({ title, html, links = [] }) {
  if (!html) return null;

  return (
    <section className="py-12 bg-white border-t border-gray-100">
      <div className="max-w-3xl mx-auto px-4 text-center">
        {title && (
          <h2 className="text-xl md:text-2xl font-semibold text-[#0B3C5D] mb-4">
            {title}
          </h2>
        )}

        <div
          className="text-gray-600 text-sm leading-relaxed space-y-3"
          dangerouslySetInnerHTML={{
            __html: injectInternalLinks(html, Array.isArray(links) ? links : []),
          }}
        />
      </div>
    </section>
  );
}
