import { absoluteLocalizedUrl, createMetadata } from "@/lib/seo";

const descriptionFallbacks = {
  services: {
    ka: (name) => `${name}: SafeTech-ის სერვისები, შესაძლებლობები და ბიზნესზე მორგებული გადაწყვეტილებები.`,
    en: (name) => `${name}: SafeTech services, capabilities, and solutions tailored to business requirements.`,
    ru: (name) => `${name}: услуги, возможности и решения SafeTech для задач бизнеса.`,
  },
  projects: {
    ka: (name) => `${name}: SafeTech-ის განხორციელებული IT ინფრასტრუქტურისა და უსაფრთხოების პროექტები.`,
    en: (name) => `${name}: IT infrastructure and security projects delivered by SafeTech.`,
    ru: (name) => `${name}: реализованные SafeTech проекты IT-инфраструктуры и безопасности.`,
  },
  blog: {
    ka: (name) => `${name}: SafeTech-ის პრაქტიკული სტატიები IT ინფრასტრუქტურისა და უსაფრთხოების შესახებ.`,
    en: (name) => `${name}: practical SafeTech articles about IT infrastructure and security.`,
    ru: (name) => `${name}: практические статьи SafeTech об IT-инфраструктуре и безопасности.`,
  },
};

const titleFallbacks = {
  services: {
    ka: (name) => `${name} სერვისები`,
    en: (name) => `${name} Services`,
    ru: (name) => `${name}: услуги`,
  },
  projects: {
    ka: (name) => `${name} პროექტები`,
    en: (name) => `${name} Projects`,
    ru: (name) => `${name}: проекты`,
  },
  blog: {
    ka: (name) => `${name} სტატიები`,
    en: (name) => `${name} Articles`,
    ru: (name) => `${name}: статьи`,
  },
};

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

function fallbackDescription(kind, name, locale) {
  const group = descriptionFallbacks[kind] || descriptionFallbacks.services;
  const formatter = group[locale] || group.ka;

  return name ? formatter(name) : "";
}

function categoryTitle(kind, category, locale) {
  const name = plainText(category?.name);
  const seoTitle = plainText(category?.seo_title);

  if (seoTitle && seoTitle.toLocaleLowerCase(locale) !== name.toLocaleLowerCase(locale)) {
    return seoTitle;
  }

  const group = titleFallbacks[kind] || titleFallbacks.services;
  const formatter = group[locale] || group.ka;

  return name ? formatter(name) : seoTitle;
}

export function categoryMetadata({ category, path, locale, kind }) {
  const description = plainText(
    category?.seo_description || category?.intro_text,
  );
  const fallback = fallbackDescription(kind, category?.name, locale);

  return createMetadata({
    title: categoryTitle(kind, category, locale),
    description: description.length >= 50 ? description : fallback || description,
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
