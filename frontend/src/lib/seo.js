import { getBaseUrl } from "@/lib/config";

const SITE_NAME = "Safetech";
const DEFAULT_TITLE = "Safetech | IT სერვისები";
const DEFAULT_DESCRIPTION =
  "IT სერვისები, კომპიუტერული მხარდაჭერა, ქსელები და უსაფრთხოების სისტემები საქართველოში.";
const DEFAULT_IMAGE = "/services/1.jpg";

function cleanText(value) {
  if (typeof value !== "string") return "";

  return value.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
}

function normalizeKeywords(keywords) {
  if (!Array.isArray(keywords)) return [];

  return keywords
    .map((item) => {
      if (typeof item === "string") return item;
      if (item && typeof item === "object") return item.value || item.keyword || "";
      return "";
    })
    .map(cleanText)
    .filter(Boolean);
}

function absoluteUrl(value, baseUrl) {
  const fallback = `${baseUrl}${DEFAULT_IMAGE}`;
  const raw = cleanText(value);

  if (!raw) return fallback;

  try {
    return new URL(raw, baseUrl).toString();
  } catch {
    return fallback;
  }
}

function absoluteCanonical(value, path, baseUrl) {
  const raw = cleanText(value) || path || "/";

  try {
    return new URL(raw, baseUrl).toString();
  } catch {
    return baseUrl;
  }
}

function withBrand(title) {
  const cleanTitle = cleanText(title);

  if (!cleanTitle) return DEFAULT_TITLE;
  if (cleanTitle.toLowerCase().includes(SITE_NAME.toLowerCase())) return cleanTitle;

  return `${cleanTitle} | ${SITE_NAME}`;
}

export function normalizeSeoMeta(input = {}) {
  const source = input?.meta || input?.data?.meta || input?.data || input || {};
  const og = source?.og || {};
  const robots = typeof source?.robots === "string" ? source.robots : "";

  return {
    title: cleanText(source?.title),
    description: cleanText(source?.description),
    image: source?.image || source?.share_image || og?.image,
    keywords: normalizeKeywords(source?.keywords),
    canonical: source?.canonical,
    noindex: Boolean(source?.noindex || robots.toLowerCase().includes("noindex")),
    og,
  };
}

export function buildMetadata({
  seo,
  title,
  description,
  image,
  path = "",
  type = "website",
  keywords = [],
  canonical,
  noindex,
  og = {},
} = {}) {
  const baseUrl = getBaseUrl().replace(/\/$/, "");
  const normalized = normalizeSeoMeta(seo);
  const mergedOg = { ...normalized.og, ...og };

  const fullTitle = withBrand(title || normalized.title);
  const fullDescription =
    cleanText(description || normalized.description) || DEFAULT_DESCRIPTION;
  const fullKeywords = normalizeKeywords(keywords).length
    ? normalizeKeywords(keywords)
    : normalized.keywords;
  const fullImage = absoluteUrl(image || normalized.image || mergedOg.image, baseUrl);
  const fullUrl = absoluteCanonical(canonical || normalized.canonical, path, baseUrl);
  const shouldNoindex =
    typeof noindex === "boolean" ? noindex : normalized.noindex;

  return {
    title: fullTitle,
    description: fullDescription,
    ...(fullKeywords.length > 0 && { keywords: fullKeywords }),

    metadataBase: new URL(baseUrl),

    robots: {
      index: !shouldNoindex,
      follow: !shouldNoindex,
      googleBot: {
        index: !shouldNoindex,
        follow: !shouldNoindex,
        "max-image-preview": "large",
        "max-snippet": -1,
        "max-video-preview": -1,
      },
    },

    openGraph: {
      title: mergedOg.title ? withBrand(mergedOg.title) : fullTitle,
      description: cleanText(mergedOg.description) || fullDescription,
      url: fullUrl,
      siteName: SITE_NAME,
      images: [
        {
          url: absoluteUrl(mergedOg.image || fullImage, baseUrl),
          width: 1200,
          height: 630,
          alt: fullTitle,
        },
      ],
      locale: "ka_GE",
      type,
    },

    twitter: {
      card: "summary_large_image",
      title: fullTitle,
      description: fullDescription,
      images: [absoluteUrl(mergedOg.image || fullImage, baseUrl)],
    },

    alternates: {
      canonical: fullUrl,
    },
  };
}
