export default function robots() {
  return {
    rules: [
      {
        userAgent: "*",
        allow: "/",
      },
    ],
    sitemap: "https://safetech.ge/sitemap.xml",
  };
}