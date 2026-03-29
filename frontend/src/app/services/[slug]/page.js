// // import { notFound } from "next/navigation";


// // import { getService } from "@/lib/datafetch";

// // import FeaturesSection from "@/app/components/services/FeaturesSection";
// // import FAQSection from "@/app/components/services/FAQSection";
// // import Share from "@/app/components/Share";
// // import ServiceHero from "../../components/services/ServiceHero";
// // import Short from "../../components/services/Short";
// // import LongDesc from "../../components/services/LongDesc";
// // import ProblemsSection from "@/app/components/services/ProblemsSection";
// // import ResultsSection from "@/app/components/services/ResultsSection";
// // import TestimonialsSection from "@/app/components/services/TestimonialsSection";
// // import CTASection from "@/app/components/services/CTASection";
// // import CaseStudySection from "@/app/components/services/CaseStudySection";
// // /* =========================
// //    🔥 SEO META
// // ========================= */
// // export async function generateMetadata({ params }) {
// //   const { slug } = await params;

// //   if (!slug) return {};

// //   const data = await getService(slug);
// // console.log(data,'სერვისი')
// //   // ✅ API error safe
// //   if (!data || data.error || !data.service) {
// //     return {};
// //   }

// //   const service = data.service;

// //   const title = service?.seo?.title || service.title;
// //   const description =
// //     service?.seo?.description || service.description;

// //   const image = service?.image || "/placeholder.jpg";

// //   const url = `${process.env.NEXT_PUBLIC_SITE_URL}/services/${slug}`;

// //   return {
// //     title,
// //     description,
// //     keywords: service?.seo?.keywords?.join(", ") || "",

// //     openGraph: {
// //       title,
// //       description,
// //       url,
// //       images: [image],
// //       type: "website",
// //     },

// //     twitter: {
// //       card: "summary_large_image",
// //       title,
// //       description,
// //       images: [image],
// //     },
// //   };
// // }

// // /* =========================
// //    PAGE
// // ========================= */
// // export default async function ServicePage({ params }) {
// //   const { slug } = await params; // ✅ სწორი

// //   if (!slug) return notFound();

// //   const data = await getService(slug);

// //   // ✅ API error safe (ძალიან მნიშვნელოვანი)
// //   if (!data || data.error || !data.service) {
// //     return notFound();
// //   }

// //   const service = data.service;
// //   const short=service?.short_description;
// //     const long=service?.long_description;
// //   /* =========================
// //      SAFE DATA
// //   ========================== */
// //   const features = Array.isArray(service.features)
// //     ? service.features
// //     : [];

// //   const faq = Array.isArray(service.faq)
// //     ? service.faq
// //     : [];

// //   const seoContent = Array.isArray(service?.seo?.content)
// //     ? service.seo.content
// //     : [];

// //   const links = Array.isArray(service?.seo?.internal_links)
// //     ? service.seo.internal_links
// //     : [];

// //   const url = `${process.env.NEXT_PUBLIC_SITE_URL}/services/${slug}`;

// //   /* =========================
// //      🔥 SCHEMA
// //   ========================== */
// //   const serviceSchema = {
// //     "@context": "https://schema.org",
// //     "@type": "Service",
// //     name: service.title,
// //     description: service.description,
// //     url,
// //     image: service.image,
// //     provider: {
// //       "@type": "Organization",
// //       name: "Safetech",
// //       url: process.env.NEXT_PUBLIC_SITE_URL,
// //     },
// //   };

// //   const faqSchema =
// //     faq.length > 0
// //       ? {
// //           "@context": "https://schema.org",
// //           "@type": "FAQPage",
// //           mainEntity: faq.map((item) => ({
// //             "@type": "Question",
// //             name: item.q,
// //             acceptedAnswer: {
// //               "@type": "Answer",
// //               text: item.a,
// //             },
// //           })),
// //         }
// //       : null;

// //   return (
// //     <main>

// //       {/* 🔥 SERVICE SCHEMA */}
// //       <script
// //         type="application/ld+json"
// //         dangerouslySetInnerHTML={{
// //           __html: JSON.stringify(serviceSchema),
// //         }}
// //       />

// //       {/* 🔥 FAQ SCHEMA */}
// //       {faqSchema && (
// //         <script
// //           type="application/ld+json"
// //           dangerouslySetInnerHTML={{
// //             __html: JSON.stringify(faqSchema),
// //           }}
// //         />
// //       )}
// // {/* <ServiceHero service={service} />

// // <Share data={data?.share ?? []} url={url} />

// // <Short short={short} /> */}

// // {/* 🔴 PROBLEMS */}
// // {/* {service.problems?.length > 0 && (
// //   <ProblemsSection problems={service.problems} />
// // )} */}

// // {/* <LongDesc long={long} /> */}

// // {/* 🟢 FEATURES */}
// // {/* {features.length > 0 && (
// //   <FeaturesSection features={features} />
// // )} */}

// // {/* 🟡 RESULTS */}
// // {/* {service.results?.length > 0 && (
// //   <ResultsSection results={service.results} />
// // )} */}

// // {/* 💼 CASE STUDY */}
// // {/* {service.case_study?.title && (
// //   <CaseStudySection data={service.case_study} />
// // )} */}

// // {/* ⭐ TESTIMONIALS */}
// // {/* {service.testimonials?.length > 0 && (
// //   <TestimonialsSection items={service.testimonials} />
// // )} */}

// // {/* ❓ FAQ */}
// // {/* {faq.length > 0 && (
// //   <FAQSection faq={faq} />
// // )} */}

// // {/* 🎯 CTA */}
// // {/* <CTASection service={service} /> */}



// // <ServiceHero service={service} />

// // <Share data={data?.share ?? []} url={url} />

// // <Short short={short} />

// // {/* 🔴 PROBLEMS */}
// // {service.problems?.length > 0 && (
// //   <ProblemsSection problems={service.problems} />
// // )}

// // {/* 🟢 FEATURES */}
// // {features.length > 0 && (
// //   <FeaturesSection features={features} />
// // )}

// // {/* 🟡 RESULTS */}
// // {service.results?.length > 0 && (
// //   <ResultsSection results={service.results} />
// // )}

// // {/* 💼 CASE STUDY */}
// // {service.case_study?.title && (
// //   <CaseStudySection data={service.case_study} />
// // )}

// // {/* ⭐ TESTIMONIALS */}
// // {service.testimonials?.length > 0 && (
// //   <TestimonialsSection items={service.testimonials} />
// // )}

// // {/* 🔥 ახლა აქ უნდა მოვიდეს */}
// // <LongDesc long={long} />

// // {/* ❓ FAQ */}
// // {faq.length > 0 && (
// //   <FAQSection faq={faq} />
// // )}

// // {/* 🎯 CTA */}
// // <CTASection service={service} />

// //     </main>
// //   );
// // }



// import { notFound } from "next/navigation";
// import { getService } from "@/lib/datafetch";

// import FeaturesSection from "@/app/components/services/FeaturesSection";
// import FAQSection from "@/app/components/services/FAQSection";
// import Share from "@/app/components/Share";
// import ServiceHero from "../../components/services/ServiceHero";
// import Short from "../../components/services/Short";
// import LongDesc from "../../components/services/LongDesc";
// import ProblemsSection from "@/app/components/services/ProblemsSection";
// import ResultsSection from "@/app/components/services/ResultsSection";
// import TestimonialsSection from "@/app/components/services/TestimonialsSection";
// import CTASection from "@/app/components/services/CTASection";
// import CaseStudySection from "@/app/components/services/CaseStudySection";

// const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
// const DEFAULT_IMAGE = "/images/service-placeholder.webp";
// console.log('ძირითადი ბმული',BASE_URL)
// /* =========================
//    🔥 METADATA (FIXED)
// ========================= */
// export async function generateMetadata({ params }) {
//   const { slug } = await params;

//   if (!slug) return {};

//   const data = await getService(slug);

//   if (!data || data.error || !data.service) {
//     return {};
//   }

//   const service = data.service;

//   const title = service?.seo?.title || service.title;
//   const description =
//     service?.seo?.description ||
//     service.short_description ||
//     service.long_description;

//   const image =
//     service?.image
//       ? service.image
//       : `${BASE_URL}${DEFAULT_IMAGE}`;

//   const url = `${BASE_URL}/services/${slug}`;

//   const keywords = Array.isArray(service?.seo?.keywords)
//     ? service.seo.keywords.join(", ")
//     : "";

//   return {
//     title,
//     description,
//     keywords,

//     alternates: {
//       canonical: url,
//     },

//     openGraph: {
//       title,
//       description,
//       url,
//       type: "website",
//       images: [
//         {
//           url: image,
//           width: 1200,
//           height: 630,
//         },
//       ],
//     },

//     twitter: {
//       card: "summary_large_image",
//       title,
//       description,
//       images: [image],
//     },
//   };
// }

// /* =========================
//    🔥 SCHEMA (UPGRADED)
// ========================= */
// function buildServiceSchema(service, url) {
//   return {
//     "@context": "https://schema.org",
//     "@type": "Service",

//     name: service.title,

//     description:
//       service.short_description ||
//       service.long_description,

//     image:
//       service.image ||
//       `${BASE_URL}${DEFAULT_IMAGE}`,

//     url,

//     provider: {
//       "@type": "Organization",
//       name: "Safetech",
//       url: BASE_URL,
//     },

//     areaServed: {
//       "@type": "Place",
//       name: "Tbilisi",
//     },

//     offers: {
//       "@type": "Offer",
//       availability: "https://schema.org/InStock",
//     },
//   };
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function ServicePage({ params }) {
//   const { slug } = await params;

//   if (!slug) return notFound();

//   const data = await getService(slug);

//   if (!data || data.error || !data.service) {
//     return notFound();
//   }

//   const service = data.service;

//   const url = `${BASE_URL}/services/${slug}`;

//   const features = Array.isArray(service.features)
//     ? service.features
//     : [];

//   const faq = Array.isArray(service.faq)
//     ? service.faq
//     : [];

//   const short = service?.short_description;
//   const long = service?.long_description;

//   /* 🔥 SCHEMA */
//   const serviceSchema = buildServiceSchema(service, url);

//   const faqSchema =
//     faq.length > 0
//       ? {
//           "@context": "https://schema.org",
//           "@type": "FAQPage",
//           mainEntity: faq.map((item) => ({
//             "@type": "Question",
//             name: item.q,
//             acceptedAnswer: {
//               "@type": "Answer",
//               text: item.a,
//             },
//           })),
//         }
//       : null;

//   return (
//     <main>

//       {/* 🔥 SERVICE SCHEMA */}
//       <script
//         type="application/ld+json"
//         dangerouslySetInnerHTML={{
//           __html: JSON.stringify(serviceSchema),
//         }}
//       />

//       {/* 🔥 FAQ SCHEMA */}
//       {faqSchema && (
//         <script
//           type="application/ld+json"
//           dangerouslySetInnerHTML={{
//             __html: JSON.stringify(faqSchema),
//           }}
//         />
//       )}

//       <ServiceHero service={service} />

// <Share data={data?.share ?? []} />

//       <Short short={short} />

//       {service.problems?.length > 0 && (
//         <ProblemsSection problems={service.problems} />
//       )}

//       {features.length > 0 && (
//         <FeaturesSection features={features} />
//       )}

//       {service.results?.length > 0 && (
//         <ResultsSection results={service.results} />
//       )}

//       {service.case_study?.title && (
//         <CaseStudySection data={service.case_study} />
//       )}

//       {service.testimonials?.length > 0 && (
//         <TestimonialsSection items={service.testimonials} />
//       )}

//       <LongDesc long={long} />

//       {faq.length > 0 && (
//         <FAQSection faq={faq} />
//       )}

//       <CTASection service={service} />

//     </main>
//   );
// }

import { notFound } from "next/navigation";
import { getService } from "@/lib/datafetch";

import FeaturesSection from "@/app/components/services/FeaturesSection";
import FAQSection from "@/app/components/services/FAQSection";
import Share from "@/app/components/Share";
import ServiceHero from "../../components/services/ServiceHero";
import Short from "../../components/services/Short";
import LongDesc from "../../components/services/LongDesc";
import ProblemsSection from "@/app/components/services/ProblemsSection";
import ResultsSection from "@/app/components/services/ResultsSection";
import TestimonialsSection from "@/app/components/services/TestimonialsSection";
import CTASection from "@/app/components/services/CTASection";
import CaseStudySection from "@/app/components/services/CaseStudySection";
import { getCurrentUrl } from "../../../lib/getUrl";

const DEFAULT_IMAGE = "/images/service-placeholder.webp";

/* =========================
   🔥 METADATA (FINAL FIX)
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) return {};

  const data = await getService(slug);

  if (!data || data.error || !data.service) {
    return {};
  }

  const service = data.service;
  const seo = service?.seo?.meta || {};

  const title = seo.title || service.title;

  const description =
    seo.description ||
    service.short_description ||
    service.long_description;

  // ✅ აღარ ვამატებთ BASE_URL-ს
  const image =
    service.image ||
    DEFAULT_IMAGE;

  // ✅ 🔥 მთავარი FIX
  const url = await getCurrentUrl(`/services/${slug}`);

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
      type: "website",
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
}

/* =========================
   🔥 SCHEMA (FIXED)
========================= */
function buildServiceSchema(service, url) {
  return {
    "@context": "https://schema.org",
    "@type": "Service",

    name: service.title,

    description:
      service.short_description || service.long_description,

    // ✅ აღარ ვამატებთ BASE_URL-ს
    image: service.image || DEFAULT_IMAGE,

    url,

    provider: {
      "@type": "Organization",
      name: "Safetech",

      // ✅ აქაც არ გვჭირდება BASE_URL
      url: url,
    },
  };
}
/* =========================
   PAGE
========================= */
export default async function ServicePage({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const data = await getService(slug);

  if (!data || data.error || !data.service) {
    return notFound();
  }

  const service = data.service;

const url = await getCurrentUrl(`/services/${slug}`);
  const features = Array.isArray(service.features)
    ? service.features
    : [];

  const faq = Array.isArray(service.faq)
    ? service.faq
    : [];

  const short = service?.short_description;
  const long = service?.long_description;

  /* 🔥 SCHEMA */
  const serviceSchema = buildServiceSchema(service, url);

  const faqSchema =
    faq.length > 0
      ? {
          "@context": "https://schema.org",
          "@type": "FAQPage",
          mainEntity: faq.map((item) => ({
            "@type": "Question",
            name: item.q,
            acceptedAnswer: {
              "@type": "Answer",
              text: item.a,
            },
          })),
        }
      : null;

  return (
    <main>

      {/* 🔥 SERVICE SCHEMA */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify(serviceSchema),
        }}
      />

      {/* 🔥 FAQ SCHEMA */}
      {faqSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(faqSchema),
          }}
        />
      )}

      <ServiceHero service={service} />

      {/* ✅ CRITICAL FIX */}
      <Share data={data?.share ?? {}} url={url} />

      <Short short={short} />

      {service.problems?.length > 0 && (
        <ProblemsSection problems={service.problems} />
      )}

      {features.length > 0 && (
        <FeaturesSection features={features} />
      )}

      {service.results?.length > 0 && (
        <ResultsSection results={service.results} />
      )}

      {service.case_study?.title && (
        <CaseStudySection data={service.case_study} />
      )}

      {service.testimonials?.length > 0 && (
        <TestimonialsSection items={service.testimonials} />
      )}

      <LongDesc long={long} />

      {faq.length > 0 && (
        <FAQSection faq={faq} />
      )}

      <CTASection service={service} />

    </main>
  );
}