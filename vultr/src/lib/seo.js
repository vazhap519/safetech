

// // export function buildMetadata({
// //   title,
// //   description,
// //   image,
// //   path = "",
// //   type = "website", // 🔥 article / website
// // }) {
// //   const baseUrl = getBaseUrl();

// //   /*
// //   |--------------------------------------------------------------------------
// //   | FALLBACKS
// //   |--------------------------------------------------------------------------
// //   */

// //   const fullTitle = title
// //     ? `${title} | Safetech`
// //     : "Safetech | IT სერვისები";

// //   const fullDescription =
// //     description || "IT და უსაფრთხოების სერვისები საქართველოში";

// //   const fullImage = image
// //     ? image.startsWith("http")
// //       ? image
// //       : `${baseUrl}${image}`
// //     : `${baseUrl}/default-og.jpg`; // 🔥 default image

// //   const fullUrl = `${baseUrl}${path}`;

// //   /*
// //   |--------------------------------------------------------------------------
// //   | METADATA
// //   |--------------------------------------------------------------------------
// //   */

// //   return {
// //     title: fullTitle,
// //     description: fullDescription,

// //     metadataBase: new URL(baseUrl),

// //     openGraph: {
// //       title: fullTitle,
// //       description: fullDescription,
// //       url: fullUrl,
// //       siteName: "Safetech",
// //       images: [
// //         {
// //           url: fullImage,
// //           width: 1200,
// //           height: 630,
// //         },
// //       ],
// //       locale: "ka_GE",
// //       type,
// //     },

// //     twitter: {
// //       card: "summary_large_image",
// //       title: fullTitle,
// //       description: fullDescription,
// //       images: [fullImage],
// //     },

// //     alternates: {
// //       canonical: fullUrl,
// //     },
// //   };
// // }


// // lib/seo.js
//  import { getBaseUrl } from "@/lib/config";
// export function buildMetadata({
//   title,
//   description,
//   image,
//   path = "",
//   type = "website",
// }) {
//   const baseUrl = getBaseUrl();

//   const fullTitle = title
//     ? `${title} | Safetech`
//     : "Safetech | IT სერვისები";

//   const fullDescription =
//     description || "IT და უსაფრთხოების სერვისები საქართველოში";

//   const fullImage = image
//     ? image.startsWith("http")
//       ? image
//       : `${baseUrl}${image}`
//     : `${baseUrl}/default-og.jpg`;

//   const fullUrl = `${baseUrl}${path}`;

//   return {
//     title: fullTitle,
//     description: fullDescription,

//     metadataBase: new URL(baseUrl),

//     openGraph: {
//       title: fullTitle,
//       description: fullDescription,
//       url: fullUrl,
//       siteName: "Safetech",
//       images: [
//         {
//           url: fullImage,
//           width: 1200,
//           height: 630,
//         },
//       ],
//       locale: "ka_GE",
//       type,
//     },

//     twitter: {
//       card: "summary_large_image",
//       title: fullTitle,
//       description: fullDescription,
//       images: [fullImage],
//     },

//     alternates: {
//       canonical: fullUrl,
//     },
//   };
// }

import { getBaseUrl } from "@/lib/config";

export function buildMetadata({
  title,
  description,
  image,
  path = "",
  type = "website",
  keywords = [],
  canonical,
  noindex = false,
  og = {},
}) {
  const baseUrl = getBaseUrl();

  /*
  |--------------------------------------------------------------------------
  | FALLBACKS
  |--------------------------------------------------------------------------
  */
  const fullTitle = title
    ? `${title} | Safetech`
    : "Safetech | IT სერვისები";

  const fullDescription =
    description || "IT და უსაფრთხოების სერვისები საქართველოში";

  const fullImage = image
    ? image.startsWith("http")
      ? image
      : `${baseUrl}${image}`
    : `${baseUrl}/default-og.jpg`;

  const fullUrl = canonical || `${baseUrl}${path}`;

  /*
  |--------------------------------------------------------------------------
  | METADATA (FINAL)
  |--------------------------------------------------------------------------
  */
  return {
    title: fullTitle,
    description: fullDescription,
    keywords,

    metadataBase: new URL(baseUrl),

    robots: noindex ? "noindex, nofollow" : "index, follow",

    openGraph: {
      title: og.title || fullTitle,
      description: og.description || fullDescription,
      url: fullUrl,
      siteName: "Safetech",
      images: [
        {
          url: og.image || fullImage,
          width: 1200,
          height: 630,
        },
      ],
      locale: "ka_GE",
      type,
    },

    twitter: {
      card: "summary_large_image",
      title: fullTitle,
      description: fullDescription,
      images: [og.image || fullImage],
    },

    alternates: {
      canonical: fullUrl,
    },
  };
}