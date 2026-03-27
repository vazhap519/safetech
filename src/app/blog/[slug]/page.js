// import { notFound } from "next/navigation";
// import { getBlogPost } from "@/lib/datafetch";
// import SEOSection from "@/app/components/SEOSection";
// import { injectInternalLinks } from "@/lib/internalLinks";

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

//     const title = post.seo?.title || post.title;
//     const description = post.seo?.description || post.excerpt;

//     const image =
//       post.seo?.image ||
//       post.image ||
//       `${BASE_URL}${DEFAULT_IMAGE}`;

//     const keywords = Array.isArray(post.seo?.keywords)
//       ? post.seo.keywords
//           .map((k) => k?.value?.trim())
//           .filter(Boolean)
//       : [];

//     return {
//       title,
//       description,
//       keywords,

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
//     console.error("❌ METADATA ERROR:", e);

//     return {
//       title: "ბლოგი",
//       description: "Safetech ბლოგი",
//     };
//   }
// }

// /* =========================
//    🔥 SCHEMA
// ========================= */
// function buildSchema(post, toc) {
//   const url = `${BASE_URL}/blog/${post.slug}`;

//   return {
//     "@context": "https://schema.org",
//     "@graph": [
//       {
//         "@type": "Article",
//         headline: post.seo?.title || post.title,
//         description: post.seo?.description || post.excerpt,
//         image: post.image,
//         author: {
//           "@type": "Person",
//           name: post.author?.name || "Safetech",
//         },
//         publisher: {
//           "@type": "Organization",
//           name: "Safetech",
//         },
//         datePublished: post.published_year,
//         mainEntityOfPage: url,
//       },
//       {
//         "@type": "BreadcrumbList",
//         itemListElement: [
//           { "@type": "ListItem", position: 1, name: "მთავარი", item: BASE_URL },
//           { "@type": "ListItem", position: 2, name: "ბლოგი", item: `${BASE_URL}/blog` },
//           { "@type": "ListItem", position: 3, name: post.title, item: url },
//         ],
//       },

//       /* 🔥 OPTIONAL TOC SCHEMA */
//       ...(toc.length > 0
//         ? [{
//             "@type": "ItemList",
//             name: "Table of Contents",
//             itemListElement: toc.map((item, i) => ({
//               "@type": "ListItem",
//               position: i + 1,
//               name: item.title,
//               item: `${url}#${item.id}`,
//             })),
//           }]
//         : []),
//     ],
//   };
// }

// /* =========================
//    🔥 TOC GENERATOR
// ========================= */
// function generateTOC(sections = []) {
//   return sections
//     .filter((s) => s?.title)
//     .map((s, index) => ({
//       id: `section-${index}`,
//       title: s.title,
//     }));
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function BlogDetailPage({ params }) {
//   const { slug } = await params;

//   if (!slug || typeof slug !== "string") {
//     return notFound();
//   }

//   const res = await getBlogPost(slug);

//   if (!res || res.error || !res.data) {
//     return notFound();
//   }

//   const post = res.data;

//   const toc = generateTOC(post.sections);
//   const schema = buildSchema(post, toc);

//   const internalLinks = [
//     { keyword: "კამერები", url: "/services/cameras" },
//     { keyword: "უსაფრთხოება", url: "/services/security" },
//     ...(post.related || []).map((r) => ({
//       keyword: r.title,
//       url: `/blog/${r.slug}`,
//     })),
//   ];

//   return (
//     <>
//       {/* 🔥 JSON-LD */}
//       <script
//         type="application/ld+json"
//         dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
//       />

//       <main className="py-20 bg-[#F8FAFC]">
//         <article className="max-w-4xl mx-auto px-4">

//           {/* TITLE */}
//           <h1 className="text-4xl font-bold text-[#0B3C5D]">
//             {post.title}
//           </h1>

//           {/* IMAGE */}
//           <img
//             src={post.image || DEFAULT_IMAGE}
//             alt={post.title}
//             className="mt-6 rounded-xl w-full"
//           />

//           {/* META */}
//           <div className="mt-4 flex gap-3 text-sm text-gray-500">
//             <span>📅 {post.published_year}</span>
//             <span>📖 {post.reading_time} წუთი</span>
//           </div>

//           {/* EXCERPT */}
//           <p className="mt-6 text-lg text-gray-600">
//             {post.excerpt}
//           </p>

//           {/* 🔥 TABLE OF CONTENTS */}
//           {toc.length > 0 && (
//             <div className="mt-10 p-6 bg-white rounded-xl border">
//               <h2 className="text-xl font-semibold text-[#0B3C5D]">
//                 სარჩევი
//               </h2>

//               <ul className="mt-4 space-y-2">
//                 {toc.map((item) => (
//                   <li key={item.id}>
//                     <a
//                       href={`#${item.id}`}
//                       className="text-[#00C2A8] hover:underline"
//                     >
//                       {item.title}
//                     </a>
//                   </li>
//                 ))}
//               </ul>
//             </div>
//           )}

//           {/* CONTENT */}
//           <div className="mt-10 space-y-6 text-gray-700">
//             {post.sections?.map((section, i) => {
//               const html = injectInternalLinks(
//                 section.content,
//                 internalLinks
//               );

//               return (
//                 <div key={i} id={`section-${i}`}>

//                   {section.title && (
//                     <h2 className="text-2xl font-bold text-[#0B3C5D] mt-10">
//                       {section.title}
//                     </h2>
//                   )}

//                   <div
//                     className="mt-4"
//                     dangerouslySetInnerHTML={{ __html: html }}
//                   />
//                 </div>
//               );
//             })}
//           </div>

//           {/* 🔥 SEO SECTION */}
//           <SEOSection
//             title="დამატებითი ინფორმაცია"
//             paragraphs={post.seo?.content || [post.excerpt]}
//             links={internalLinks}
//           />

//         </article>
//       </main>
//     </>
//   );
// }











import { notFound } from "next/navigation";
import Link from "next/link";
import { getBlogPost } from "@/lib/datafetch";
import SEOSection from "@/app/components/SEOSection";
import { injectInternalLinks } from "@/lib/internalLinks";

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

/* =========================
   🔥 METADATA
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

    const title = post.seo?.title || post.title;
    const description = post.seo?.description || post.excerpt;

    const image =
      post.seo?.image ||
      post.image ||
      `${BASE_URL}${DEFAULT_IMAGE}`;

    const keywords = Array.isArray(post.seo?.keywords)
      ? post.seo.keywords
          .map((k) => k?.value?.trim())
          .filter(Boolean)
      : [];

    return {
      title,
      description,
      keywords,

      alternates: {
        canonical: `${BASE_URL}/blog/${slug}`,
      },

      openGraph: {
        title,
        description,
        url: `${BASE_URL}/blog/${slug}`,
        type: "article",
        images: [
          {
            url: image,
            width: 1200,
            height: 630,
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

  } catch (e) {
    console.error("❌ METADATA ERROR:", e);

    return {
      title: "ბლოგი",
      description: "Safetech ბლოგი",
    };
  }
}

/* =========================
   🔥 SCHEMA
========================= */
function buildSchema(post, toc) {
  const url = `${BASE_URL}/blog/${post.slug}`;

  return {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Article",
        headline: post.seo?.title || post.title,
        description: post.seo?.description || post.excerpt,
        image: post.image,
        author: {
          "@type": "Person",
          name: post.author?.name || "Safetech",
        },
        publisher: {
          "@type": "Organization",
          name: "Safetech",
        },
        datePublished: post.published_year,
        mainEntityOfPage: url,
      },
      {
        "@type": "BreadcrumbList",
        itemListElement: [
          { "@type": "ListItem", position: 1, name: "მთავარი", item: BASE_URL },
          { "@type": "ListItem", position: 2, name: "ბლოგი", item: `${BASE_URL}/blog` },
          { "@type": "ListItem", position: 3, name: post.title, item: url },
        ],
      },

      ...(toc.length > 0
        ? [{
            "@type": "ItemList",
            name: "Table of Contents",
            itemListElement: toc.map((item, i) => ({
              "@type": "ListItem",
              position: i + 1,
              name: item.title,
              item: `${url}#${item.id}`,
            })),
          }]
        : []),
    ],
  };
}

/* =========================
   🔥 TOC
========================= */
function generateTOC(sections = []) {
  return sections
    .filter((s) => s?.title)
    .map((s, index) => ({
      id: `section-${index}`,
      title: s.title,
    }));
}

/* =========================
   PAGE
========================= */
export default async function BlogDetailPage({ params }) {
  const { slug } = await params;

  if (!slug || typeof slug !== "string") {
    return notFound();
  }

  const res = await getBlogPost(slug);

  if (!res || res.error || !res.data) {
    return notFound();
  }

  const post = res.data;

  const toc = generateTOC(post.sections);
  const schema = buildSchema(post, toc);

  /* 🔥 INTERNAL LINKS (DB-driven) */
  const internalLinks = [
    ...(post.seo?.internal_links || []),

    ...(post.related || []).map((r) => ({
      keyword: r.title,
      url: `/blog/${r.slug}`,
    })),
  ];

  return (
    <>
      {/* 🔥 JSON-LD */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
      />

      <main className="py-20 bg-[#F8FAFC]">
        <article className="max-w-4xl mx-auto px-4">

          {/* TITLE */}
          <h1 className="text-4xl font-bold text-[#0B3C5D]">
            {post.title}
          </h1>

          {/* IMAGE */}
          <img
            src={post.image || DEFAULT_IMAGE}
            alt={post.title}
            className="mt-6 rounded-xl w-full"
          />

          {/* META */}
          <div className="mt-4 flex gap-3 text-sm text-gray-500">
            <span>📅 {post.published_year}</span>
            <span>📖 {post.reading_time} წუთი</span>
          </div>

          {/* EXCERPT */}
          <p className="mt-6 text-lg text-gray-600">
            {post.excerpt}
          </p>

          {/* 🔥 TOC */}
          {toc.length > 0 && (
            <div className="mt-10 p-6 bg-white rounded-xl border">
              <h2 className="text-xl font-semibold text-[#0B3C5D]">
                სარჩევი
              </h2>

              <ul className="mt-4 space-y-2">
                {toc.map((item) => (
                  <li key={item.id}>
                    <a
                      href={`#${item.id}`}
                      className="text-[#00C2A8] hover:underline"
                    >
                      {item.title}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {/* CONTENT */}
          <div className="mt-10 space-y-6 text-gray-700">
            {post.sections?.map((section, i) => {
              const html = injectInternalLinks(
                section.content,
                internalLinks
              );

              return (
                <div key={i} id={`section-${i}`}>
                  {section.title && (
                    <h2 className="text-2xl font-bold text-[#0B3C5D] mt-10">
                      {section.title}
                    </h2>
                  )}

                  <div
                    className="mt-4"
                    dangerouslySetInnerHTML={{ __html: html }}
                  />
                </div>
              );
            })}
          </div>

          {/* 🔥 SEO SECTION */}
          <SEOSection
            title="დამატებითი ინფორმაცია"
            paragraphs={post.seo?.content || [post.excerpt]}
            links={internalLinks}
          />

          {/* 🔥 RELATED POSTS */}
          {post.related?.length > 0 && (
            <div className="mt-20">
              <h2 className="text-2xl font-bold text-[#0B3C5D]">
                მსგავსი სტატიები
              </h2>

              <div className="grid md:grid-cols-3 gap-6 mt-6">
                {post.related.map((r) => (
                  <Link
                    key={r.slug}
                    href={`/blog/${r.slug}`}
                    className="block bg-white rounded-xl shadow hover:shadow-lg transition"
                  >
                    <img
                      src={r.image}
                      alt={r.title}
                      className="rounded-t-xl h-40 w-full object-cover"
                    />

                    <div className="p-4">
                      <p className="font-semibold text-[#0B3C5D]">
                        {r.title}
                      </p>
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          )}

        </article>
      </main>
    </>
  );
}