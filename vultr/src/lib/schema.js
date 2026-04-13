export function buildSchema({
  type = "WebPage",
  title,
  description,
  image,
  url,
  datePublished,
}) {
  return {
    "@context": "https://schema.org",
    "@type": type,
    headline: title,
    description,
    image,
    url,
    datePublished,
  };
}