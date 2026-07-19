import { createMetadata } from "@/lib/seo";

export function asList(response) {
  if (Array.isArray(response)) return response;
  if (Array.isArray(response?.data)) return response.data;
  return [];
}

export function findCategory(response, slug) {
  return asList(response).find((category) => category?.slug === slug) || null;
}

export function keywordValues(category) {
  const keywords = category?.seo_keywords || category?.seo?.keywords || [];

  if (!Array.isArray(keywords)) return [];

  return keywords
    .map((item) => (typeof item === "string" ? item : item?.value))
    .filter(Boolean);
}

export function categoryMetadata({ category, path, locale }) {
  return createMetadata({
    title: category?.seo_title || category?.name || "",
    description: category?.seo_description || category?.intro_text || "",
    keywords: keywordValues(category),
    path,
    locale,
    noindex: Boolean(category?.noindex),
  });
}

export function categorySchemas({ category }) {
  const schemas = [];

  if (category?.schema) {
    schemas.push(...(Array.isArray(category.schema) ? category.schema : [category.schema]));
  }

  const faq = Array.isArray(category?.faq) ? category.faq : [];
  const mainEntity = faq
    .filter((item) => (item?.question || item?.q) && (item?.answer || item?.a))
    .map((item) => ({
      "@type": "Question",
      name: item.question || item.q,
      acceptedAnswer: {
        "@type": "Answer",
        text: item.answer || item.a,
      },
    }));

  if (mainEntity.length > 0) {
    schemas.push({
      "@context": "https://schema.org",
      "@type": "FAQPage",
      mainEntity,
    });
  }

  return schemas.filter(Boolean);
}
