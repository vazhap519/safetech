const baseUrl = "https://safetech.ge";

export function buildMetadata({
  title,
  description,
  image = "/services/1.jpg",
  path = "",
}) {
  const fullTitle = title
    ? `${title} | Safetech`
    : "Safetech | IT სერვისები";

  const fullDescription =
    description || "IT და უსაფრთხოების სერვისები საქართველოში";

  return {
    title: fullTitle,
    description: fullDescription,

    openGraph: {
      title: fullTitle,
      description: fullDescription,
      url: `${baseUrl}${path}`,
      siteName: "Safetech",
      images: [image],
      locale: "ka_GE",
      type: "website",
    },

    twitter: {
      card: "summary_large_image",
      title: fullTitle,
      description: fullDescription,
      images: [image],
    },

    alternates: {
      canonical: `${baseUrl}${path}`,
    },
  };
}