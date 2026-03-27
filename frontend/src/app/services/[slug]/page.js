import { notFound } from "next/navigation";


import { getService } from "@/lib/datafetch";

import FeaturesSection from "@/app/components/services/FeaturesSection";
import FAQSection from "@/app/components/services/FAQSection";
import SEOSection from "@/app/components/SEOSection";
import Share from "@/app/components/Share";
import ServiceHero from "../../components/services/ServiceHero";
import Short from "../../components/services/Short";
import LongDesc from "../../components/services/LongDesc";
/* =========================
   🔥 SEO META
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) return {};

  const data = await getService(slug);
console.log(data,'სერვისი')
  // ✅ API error safe
  if (!data || data.error || !data.service) {
    return {};
  }

  const service = data.service;

  const title = service?.seo?.title || service.title;
  const description =
    service?.seo?.description || service.description;

  const image = service?.image || "/placeholder.jpg";

  const url = `${process.env.NEXT_PUBLIC_SITE_URL}/services/${slug}`;

  return {
    title,
    description,
    keywords: service?.seo?.keywords?.join(", ") || "",

    openGraph: {
      title,
      description,
      url,
      images: [image],
      type: "website",
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
   PAGE
========================= */
export default async function ServicePage({ params }) {
  const { slug } = await params; // ✅ სწორი

  if (!slug) return notFound();

  const data = await getService(slug);

  // ✅ API error safe (ძალიან მნიშვნელოვანი)
  if (!data || data.error || !data.service) {
    return notFound();
  }

  const service = data.service;
  const short=service?.short_description;
    const long=service?.long_description;
  /* =========================
     SAFE DATA
  ========================== */
  const features = Array.isArray(service.features)
    ? service.features
    : [];

  const faq = Array.isArray(service.faq)
    ? service.faq
    : [];

  const seoContent = Array.isArray(service?.seo?.content)
    ? service.seo.content
    : [];

  const links = Array.isArray(service?.seo?.internal_links)
    ? service.seo.internal_links
    : [];

  const url = `${process.env.NEXT_PUBLIC_SITE_URL}/services/${slug}`;

  /* =========================
     🔥 SCHEMA
  ========================== */
  const serviceSchema = {
    "@context": "https://schema.org",
    "@type": "Service",
    name: service.title,
    description: service.description,
    url,
    image: service.image,
    provider: {
      "@type": "Organization",
      name: "Safetech",
      url: process.env.NEXT_PUBLIC_SITE_URL,
    },
  };

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

      {/* HERO */}
<ServiceHero service={service} />
      {/* SHARE */}
      <Share data={data?.share ?? []} url={url} />
<Short short={short}/>
<LongDesc long={long} />

      {/* FEATURES */}
      {features.length > 0 && (
        <FeaturesSection features={features} />
      )}



      {/* SEO CONTENT */}
      {/* {seoContent.length > 0 && (
        <SEOSection
          title={service.title}
          paragraphs={seoContent}
          links={links}
        />
      )} */}

      {/* FAQ */}
      {faq.length > 0 && (
        <FAQSection faq={faq} />
      )}

    </main>
  );
}