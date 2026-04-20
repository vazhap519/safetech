import { notFound } from "next/navigation";
import { cache } from "react";
import { getService } from "@/lib/datafetch";

import FeaturesSection from "@/app/components/services/FeaturesSection";
import FAQSection from "@/app/components/services/FAQSection";
import Share from "@/app/components/Share";
import ServiceHero from "../../components/services/ServiceHero";
import Short from "../../components/services/Short";
import LongDesc from "../../components/services/LongDesc";
import CTASection from "@/app/components/services/CTASection";
import { getCurrentUrl } from "../../../lib/getUrl";
import { buildMetadata } from "@/lib/seo";

const DEFAULT_IMAGE = "/services/1.jpg";

/* 🔥 CACHE (CRITICAL FIX) */
const getServiceCached = cache(async (slug) => {
  return await getService(slug);
});

/* =========================
   🔥 METADATA (OPTIMIZED)
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) return {};

  const data = await getServiceCached(slug);

  if (!data || data.error || !data.service) {
    return {};
  }

  const service = data.service;
  const url = await getCurrentUrl(`/services/${slug}`);
  const seo = service?.seo?.meta || {};

  return buildMetadata({
    title: seo.title || service.title,
    description: seo.description || service.short_description || service.long_description,
    image: seo.image || service.image || DEFAULT_IMAGE,
    canonical: seo.canonical || url,
    noindex: Boolean(seo.noindex),
    path: `/services/${slug}`,
    type: "website",
  });
}

/* =========================
   🔥 SCHEMA
========================= */
function buildServiceSchema(service, url) {
  return {
    "@context": "https://schema.org",
    "@type": "Service",
    name: service.title,
    description:
      service.short_description || service.long_description,
    image: service.image || DEFAULT_IMAGE,
    url,
    provider: {
      "@type": "Organization",
      name: "Safetech",
      url: process.env.NEXT_PUBLIC_SITE_URL || "https://safetech.ge",
    },
  };
}

/* =========================
   🚀 PAGE (FAST)
========================= */
export default async function ServicePage({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const data = await getServiceCached(slug);

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

  const customSchema = service?.seo?.schema;
  const serviceSchema = customSchema || buildServiceSchema(service, url);
  const serviceSchemas = Array.isArray(serviceSchema)
    ? serviceSchema.filter(Boolean)
    : [serviceSchema].filter(Boolean);

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
      {serviceSchemas.map((schema, index) => (
        <script
          key={`service-schema-${index}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(schema),
          }}
        />
      ))}

      {/* 🔥 FAQ SCHEMA */}
      {faqSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(faqSchema),
          }}
        />
      )}

      {/* 🔥 HERO (აქ არის image) */}
      <ServiceHero service={service} />

      <Short short={service.short_description} />

      {features.length > 0 && (
        <FeaturesSection features={features} />
      )}

      <LongDesc long={service.long_description} />

      {faq.length > 0 && (
        <FAQSection faq={faq} />
      )}

      <CTASection service={service} />

      {/* 🔥 SHARE */}
      <Share data={data?.share ?? {}} url={url} />

    </main>
  );
}
