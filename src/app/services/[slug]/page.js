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

const DEFAULT_IMAGE = "/images/service-placeholder.webp";

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

  const title = service.title;
  const description =
    service.short_description || service.long_description;

  const image = service.image || DEFAULT_IMAGE;

  const url = await getCurrentUrl(`/services/${slug}`);

  return {
    title,
    description,
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
      url,
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