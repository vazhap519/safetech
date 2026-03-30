import CTASection from "./components/home/CTASection";
import FAQ from "./components/home/FAQ";
import HeroSection from "./components/home/HeroSection";
import HowItWorks from "./components/home/HowItWorks";
import ServicesPreview from "./components/home/ServicesPreview";
import WhyUs from "./components/home/WhyUs";
import TrustSection from "./components/home/TrustSection";
import StatsSection from "./components/home/StatsSection";
import Testimonials from "./components/home/Testimonials";

import { getHome, getSeoByKey } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";
export const revalidate = 60;
/* =========================
   HELPER
========================= */
const hasContent = (value) => {
  if (!value) return false;

  if (Array.isArray(value)) return value.length > 0;

  if (typeof value === "object") {
    return Object.values(value).some((v) => {
      if (Array.isArray(v)) return v.length > 0;
      if (typeof v === "object" && v !== null)
        return Object.values(v).some(Boolean);
      return Boolean(v);
    });
  }

  return Boolean(value);
};

/* =========================
   SEO
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("home");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/",
  });
}

/* =========================
   PAGE
========================= */
// export default async function Home() {
//   const data = await getHome({
//     next: { revalidate: 60 },
//   });

//   return (
//     <main>

//       {/* 🔥 JSON-LD */}
//       <script
//         type="application/ld+json"
//         dangerouslySetInnerHTML={{
//           __html: JSON.stringify({
//             "@context": "https://schema.org",
//             "@type": "Organization",
//             name: "Safetech",
//             url: "https://safetech.ge",
//             logo: "https://safetech.ge/logo.png",
//             contactPoint: {
//               "@type": "ContactPoint",
//               telephone: "+995599000000",
//               contactType: "customer service",
//             },
//           }),
//         }}
//       />

//       <script
//         type="application/ld+json"
//         dangerouslySetInnerHTML={{
//           __html: JSON.stringify({
//             "@context": "https://schema.org",
//             "@type": "LocalBusiness",
//             name: "Safetech",
//             areaServed: "Georgia",
//             telephone: "+995599000000",
//           }),
//         }}
//       />
// {/* 🟢 HERO */}
// {hasContent(data?.homeHero) && (
//   <HeroSection data={data.homeHero} />
// )}

// {/* 🟡 WHY US */}
// {hasContent(data?.whyUs) && (
//   <WhyUs data={data.whyUs} />
// )}

// {/* 🟢 SERVICES */}
// {hasContent(data?.services) && (
//   <ServicesPreview data={data} />
// )}

// {/* ⚙️ HOW IT WORKS */}
// {hasContent(data?.howWork) && (
//   <HowItWorks data={data.howWork} />
// )}

// {/* 🔵 TRUST (ლოგოები) */}
// {hasContent(data?.trust) && (
//   <TrustSection data={data.trust} />
// )}

// {/* ⭐ TESTIMONIALS */}
// {hasContent(data?.testimonials) && (
//   <Testimonials items={data.testimonials} />
// )}

// {/* 🟣 STATS */}
// {hasContent(data?.stats) && (
//   <StatsSection data={data.stats} />
// )}

// {/* 🔴 CTA */}
// {hasContent(data?.Cta) && (
//   <CTASection data={data.Cta} />
// )}

// {/* ❓ FAQ */}
// {hasContent(data?.Faq) && (
//   <FAQ data={data.Faq} />
// )}

//     </main>
//   );
// }
export default async function Home() {
  const res = await getHome({
    next: { revalidate: 60 },
  });

  const data = res.data;
  const seo = res.seo;
const isEmpty =
  !hasContent(data?.homeHero) &&
  !hasContent(data?.whyUs) &&
  !hasContent(data?.services) &&
  !hasContent(data?.howWork) &&
  !hasContent(data?.trust) &&
  !hasContent(data?.testimonials) &&
  !hasContent(data?.stats) &&
  !hasContent(data?.Cta) &&
  !hasContent(data?.Faq);
  return (
    <>
      {/* ✅ DYNAMIC SCHEMA */}
   {seo?.schema && (
  Array.isArray(seo.schema) ? (
    seo.schema.map((schema, i) => (
      <script
        key={i}
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify(schema),
        }}
      />
    ))
  ) : (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{
        __html: JSON.stringify(seo.schema),
      }}
    />
  )
)}

      {/* <main>

        {hasContent(data?.homeHero) && (
          <HeroSection data={data.homeHero} />
        )}

        {hasContent(data?.whyUs) && (
          <WhyUs data={data.whyUs} />
        )}

        {hasContent(data?.services) && (
          <ServicesPreview data={data} />
        )}

        {hasContent(data?.howWork) && (
          <HowItWorks data={data.howWork} />
        )}

        {hasContent(data?.trust) && (
          <TrustSection data={data.trust} />
        )}

        {hasContent(data?.testimonials) && (
          <Testimonials items={data.testimonials} />
        )}

        {hasContent(data?.stats) && (
          <StatsSection data={data.stats} />
        )}

        {hasContent(data?.Cta) && (
          <CTASection data={data.Cta} />
        )}

        {hasContent(data?.Faq) && (
          <FAQ data={data.Faq} />
        )}

      </main> */}

      <main>

  {isEmpty && (
    <div className="min-h-[60vh] flex items-center justify-center text-center">
      <p className="text-gray-500">
        კონტენტი დროებით არ არის
      </p>
    </div>
  )}

  {hasContent(data?.homeHero) && (
    <HeroSection data={data.homeHero} />
  )}

  {hasContent(data?.whyUs) && (
    <WhyUs data={data.whyUs} />
  )}

  {hasContent(data?.services) && (
    <ServicesPreview data={data} />
  )}

  {hasContent(data?.howWork) && (
    <HowItWorks data={data.howWork} />
  )}

  {hasContent(data?.trust) && (
    <TrustSection data={data.trust} />
  )}

  {hasContent(data?.testimonials) && (
    <Testimonials items={data.testimonials} />
  )}

  {hasContent(data?.stats) && (
    <StatsSection data={data.stats} />
  )}

  {hasContent(data?.Cta) && (
    <CTASection data={data.Cta} />
  )}

  {hasContent(data?.Faq) && (
    <FAQ data={data.Faq} />
  )}

</main>
    </>
  );
}