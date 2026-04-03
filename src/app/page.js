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
import EmptyState from "../app/components/ui/EmptyState";
import  { getEmpty } from "@/lib/datafetch";  
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

export default async function Home() {
  const res = await getHome({
    next: { revalidate: 60 },
  });

  const data = res?.data || {};
  const seo = res?.seo || {};
    const empty = await getEmpty().catch(() => null);
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
  


  /* 🔥 მთავარი */
  if (!res || res.error || isEmpty) {
    return <EmptyState empty={empty} />;
  }
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



      <main>

 

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