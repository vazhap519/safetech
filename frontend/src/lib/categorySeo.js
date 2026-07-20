import { absoluteLocalizedUrl, createMetadata } from "@/lib/seo";

function plainText(value) {
  return typeof value === "string"
    ? value.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim()
    : "";
}

function asList(response) {
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

export function categorySchemas({ category, path, locale }) {
  const schemas = [];

  if (category?.schema) {
    schemas.push(...(Array.isArray(category.schema) ? category.schema : [category.schema]));
  } else if (category?.name && path) {
    const description = plainText(
      category.seo_description || category.intro_text,
    );

    schemas.push({
      "@context": "https://schema.org",
      "@type": "CollectionPage",
      name: category.name,
      ...(description ? { description } : {}),
      url: absoluteLocalizedUrl(path, locale),
    });
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
