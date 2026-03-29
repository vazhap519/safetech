

// // import { notFound } from "next/navigation";
// // import Link from "next/link";
// // import { getBlogPost, getSettings } from "@/lib/datafetch";
// // import SEOSection from "@/app/components/SEOSection";
// // import { injectInternalLinks } from "@/lib/internalLinks";
// // import Share from "../../components/Share";

// // const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
// // const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

// // /* =========================
// //    🔥 METADATA
// // ========================= */
// // export async function generateMetadata({ params }) {
// //   const { slug } = await params;

// //   if (!slug) {
// //     return {
// //       title: "ბლოგი",
// //       description: "Safetech ბლოგი",
// //     };
// //   }

// //   try {
// //     const res = await getBlogPost(slug);

// //     if (!res || res.error || !res.data) {
// //       return {
// //         title: "ბლოგი",
// //         description: "სტატია ვერ მოიძებნა",
// //       };
// //     }

// //     const post = res.data;
// //     const seo = post.seo?.meta || {};

// //     const title = seo.title || post.title;
// //     const description = seo.description || post.excerpt;

// //     const image =
// //       seo.image ||
// //       post.image ||
// //       `${BASE_URL}${DEFAULT_IMAGE}`;

// //     const keywords = Array.isArray(seo.keywords)
// //       ? seo.keywords.join(", ")
// //       : "";

// //     return {
// //       title,
// //       description,
// //       keywords,

// //       alternates: {
// //         canonical: `${BASE_URL}/blog/${slug}`,
// //       },

// //       openGraph: {
// //         title,
// //         description,
// //         url: `${BASE_URL}/blog/${slug}`,
// //         type: "article",
// //         images: [
// //           {
// //             url: image,
// //             width: 1200,
// //             height: 630,
// //           },
// //         ],
// //       },

// //       twitter: {
// //         card: "summary_large_image",
// //         title,
// //         description,
// //         images: [image],
// //       },
// //     };

// //   } catch (e) {
// //     return {
// //       title: "ბლოგი",
// //       description: "Safetech ბლოგი",
// //     };
// //   }
// // }

// // /* =========================
// //    🔥 SCHEMA
// // ========================= */
// // function buildSchema(post, toc) {
// //   const url = `${BASE_URL}/blog/${post.slug}`;
// //   const seo = post.seo?.meta || {};

// //   return {
// //     "@context": "https://schema.org",
// //     "@graph": [
// //       {
// //         "@type": "BlogPosting",
// //         headline: seo.title || post.title,
// //         description: seo.description || post.excerpt,
// //         image:
// //           seo.image ||
// //           post.image ||
// //           `${BASE_URL}${DEFAULT_IMAGE}`,
// //         author: {
// //           "@type": "Organization",
// //           name: post.author?.name || "Safetech",
// //         },
// //         publisher: {
// //           "@type": "Organization",
// //           name: "Safetech",
// //         },
// //         datePublished:
// //           post.seo_published_at ||
// //           post.created_at ||
// //           new Date().toISOString(),
// //         mainEntityOfPage: url,
// //       },
// //     ],
// //   };
// // }

// // /* =========================
// //    PAGE
// // ========================= */
// // export default async function BlogDetailPage({ params }) {
// //   const { slug } = await params;

// //   if (!slug) return notFound();

// //   const res = await getBlogPost(slug);

// //   if (!res || res.error || !res.data) {
// //     return notFound();
// //   }

// //   const post = res.data;

// //   // ✅ 🔥 FIX: settings fetch
// //   const settings = await getSettings();

// //   // ✅ 🔥 FIX: url for share
// //   const url = `${BASE_URL}/blog/${post.slug}`;

// //   return (
// //     <>
// //       <main className="py-20 bg-[#F8FAFC]">
// //         <article className="max-w-4xl mx-auto px-4">

// //           <h1 className="text-4xl font-bold text-[#0B3C5D]">
// //             {post.title}
// //           </h1>

// //           <img
// //             src={post.image || DEFAULT_IMAGE}
// //             alt={post.title}
// //             className="mt-6 rounded-xl w-full"
// //           />

// //           <p className="mt-6 text-lg text-gray-600">
// //             {post.excerpt}
// //           </p>

// //           {/* ✅ 🔥 FIXED SHARE */}
// //           <Share data={settings?.share} url={url} />

// //           {/* CONTENT */}
// //           <div className="mt-10 space-y-6 text-gray-700">
// //             {post.sections?.map((section, i) => (
// //               <div key={i}>
// //                 {section.title && (
// //                   <h2 className="text-2xl font-bold text-[#0B3C5D] mt-10">
// //                     {section.title}
// //                   </h2>
// //                 )}

// //                 <div
// //                   className="mt-4"
// //                   dangerouslySetInnerHTML={{
// //                     __html: injectInternalLinks(section.content, []),
// //                   }}
// //                 />
// //               </div>
// //             ))}
// //           </div>

// //           <SEOSection
// //             title="დამატებითი ინფორმაცია"
// //             paragraphs={post.seo?.content || [post.excerpt]}
// //             links={[]}
// //           />

// //         </article>
// //       </main>
// //     </>
// //   );
// // }


// import { notFound } from "next/navigation";
// import Link from "next/link";
// import { getBlogPost, getSettings } from "@/lib/datafetch";
// import SEOSection from "@/app/components/SEOSection";
// import { injectInternalLinks } from "@/lib/internalLinks";
// import Share from "../../components/Share";

// const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
// const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

// /* =========================
//    🔥 METADATA
// ========================= */
// export async function generateMetadata({ params }) {
//   const { slug } = await params;

//   if (!slug) {
//     return {
//       title: "ბლოგი",
//       description: "Safetech ბლოგი",
//     };
//   }

//   try {
//     const res = await getBlogPost(slug);

//     if (!res || res.error || !res.data) {
//       return {
//         title: "ბლოგი",
//         description: "სტატია ვერ მოიძებნა",
//       };
//     }

//     const post = res.data;
//     const seo = post.seo?.meta || {};

//     const title = seo.title || post.title;
//     const description = seo.description || post.excerpt;

//     const image =
//       seo.image ||
//       post.image ||
//       `${BASE_URL}${DEFAULT_IMAGE}`;

//     return {
//       title,
//       description,

//       alternates: {
//         canonical: `${BASE_URL}/blog/${slug}`,
//       },

//       openGraph: {
//         title,
//         description,
//         url: `${BASE_URL}/blog/${slug}`,
//         type: "article",
//         images: [
//           {
//             url: image,
//             width: 1200,
//             height: 630,
//           },
//         ],
//       },

//       twitter: {
//         card: "summary_large_image",
//         title,
//         description,
//         images: [image],
//       },
//     };
//   } catch (e) {
//     return {
//       title: "ბლოგი",
//       description: "Safetech ბლოგი",
//     };
//   }
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function BlogDetailPage({ params }) {
//   const { slug } = await params;

//   if (!slug) return notFound();

//   const res = await getBlogPost(slug);

//   if (!res || res.error || !res.data) {
//     return notFound();
//   }

//   const post = res.data;

//   // ✅ settings
//   const settings = await getSettings();

//   return (
//     <main className="py-20 bg-[#F8FAFC]">
//       <article className="max-w-4xl mx-auto px-4">

//         <h1 className="text-4xl font-bold text-[#0B3C5D]">
//           {post.title}
//         </h1>

//         <img
//           src={post.image || DEFAULT_IMAGE}
//           alt={post.title}
//           className="mt-6 rounded-xl w-full"
//         />

//         <p className="mt-6 text-lg text-gray-600">
//           {post.excerpt}
//         </p>

//         {/* 🔥 FIXED SHARE (NO URL!) */}
//         <Share data={settings?.share} />

//         {/* CONTENT */}
//         <div className="mt-10 space-y-6 text-gray-700">
//           {post.sections?.map((section, i) => (
//             <div key={i}>
//               {section.title && (
//                 <h2 className="text-2xl font-bold text-[#0B3C5D] mt-10">
//                   {section.title}
//                 </h2>
//               )}

//               <div
//                 className="mt-4"
//                 dangerouslySetInnerHTML={{
//                   __html: injectInternalLinks(section.content, []),
//                 }}
//               />
//             </div>
//           ))}
//         </div>

//         <SEOSection
//           title="დამატებითი ინფორმაცია"
//           paragraphs={post.seo?.content || [post.excerpt]}
//           links={[]}
//         />

//       </article>
//     </main>
//   );
// }


import { notFound } from "next/navigation";
import { getBlogPost, getSettings } from "@/lib/datafetch";
import SEOSection from "@/app/components/SEOSection";
import { injectInternalLinks } from "@/lib/internalLinks";
import Share from "../../components/Share";
import { getCurrentUrl } from "../../../lib/getUrl";

const DEFAULT_IMAGE = "/images/blog-placeholder.webp"
/* =========================
   🔥 METADATA (FINAL FIX)
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) {
    return {
      title: "ბლოგი",
      description: "Safetech ბლოგი",
    };
  }

  try {
    const res = await getBlogPost(slug);

    if (!res || res.error || !res.data) {
      return {
        title: "ბლოგი",
        description: "სტატია ვერ მოიძებნა",
      };
    }

    const post = res.data;
    const seo = post?.seo?.meta || {};

    const title = seo.title || post.title;
    const description = seo.description || post.excerpt;

    // ✅ აღარ ვაწყობთ ხელით URL-ს
    const image =
      seo.image ||
      post.image ||
      DEFAULT_IMAGE;

    // ✅ 🔥 მთავარი FIX
    const url = await getCurrentUrl(`/blog/${slug}`);

    const keywords = Array.isArray(seo.keywords)
      ? seo.keywords.join(", ")
      : "";

    return {
      title,
      description,
      keywords,

      alternates: {
        canonical: url,
      },

      openGraph: {
        title,
        description,
        url,
        type: "article",
        images: [
          {
            url: image,
            width: 1200,
            height: 630,
            alt: title,
          },
        ],
      },

      twitter: {
        card: "summary_large_image",
        title,
        description,
        images: [image],
      },
    };

  } catch {
    return {
      title: "ბლოგი",
      description: "Safetech ბლოგი",
    };
  }
}

/* =========================
   🔥 SCHEMA (FIXED)
========================= */
function buildSchema(post, url) {
  const seo = post?.seo?.meta || {};

  return {
    "@context": "https://schema.org",
    "@type": "BlogPosting",

    headline: seo.title || post.title,
    description: seo.description || post.excerpt,

    // ✅ აღარ ვამატებთ BASE_URL-ს
    image: post.image || DEFAULT_IMAGE,

    author: {
      "@type": "Organization",
      name: post.author?.name || "Safetech",
    },

    publisher: {
      "@type": "Organization",
      name: "Safetech",
    },

    datePublished:
      post.created_at || new Date().toISOString(),

    mainEntityOfPage: url,
  };
}
/* =========================
   PAGE
========================= */
export default async function BlogDetailPage({ params }) {
  const { slug } =await params;

  if (!slug) return notFound();

  const res = await getBlogPost(slug);

  if (!res || res.error || !res.data) {
    return notFound();
  }

  const post = res.data;

  const settings = await getSettings();

const url = await getCurrentUrl(`/blog/${post.slug}`);
  const schema = buildSchema(post, url);

  return (
    <main className="py-20 bg-[#F8FAFC]">
      {/* 🔥 SCHEMA */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify(schema),
        }}
      />

      <article className="max-w-4xl mx-auto px-4">

        <h1 className="text-4xl font-bold text-[#0B3C5D]">
          {post.title}
        </h1>

        <img
          src={
            post.image?.startsWith("http")
              ? post.image
              : `${BASE_URL}${post.image || DEFAULT_IMAGE}`
          }
          alt={post.title}
          className="mt-6 rounded-xl w-full"
        />

        <p className="mt-6 text-lg text-gray-600">
          {post.excerpt}
        </p>

        {/* ✅ CRITICAL FIX */}
        <Share data={settings?.share ?? {}} url={url} />

        {/* CONTENT */}
        <div className="mt-10 space-y-6 text-gray-700">
          {post.sections?.map((section, i) => (
            <div key={i}>
              {section.title && (
                <h2 className="text-2xl font-bold text-[#0B3C5D] mt-10">
                  {section.title}
                </h2>
              )}

              <div
                className="mt-4"
                dangerouslySetInnerHTML={{
                  __html: injectInternalLinks(section.content, []),
                }}
              />
            </div>
          ))}
        </div>

        <SEOSection
          title="დამატებითი ინფორმაცია"
          paragraphs={post.seo?.content || [post.excerpt]}
          links={[]}
        />

      </article>
    </main>
  );
}