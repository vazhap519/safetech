import { notFound } from "next/navigation";
import Image from "next/image";

import { buildMetadata } from "@/lib/seo";
import { getService } from "@/lib/datafetch";

import FeaturesSection from "@/app/components/services/FeaturesSection";
import FAQSection from "@/app/components/services/FAQSection";
import SEOSection from "@/app/components/services/SEOSection";
import Share from "@/app/components/Share";

/* =========================
   META SEO
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;

  const data = await getService(slug);
  const service = data?.service;

  if (!service) return {};

  return buildMetadata({
    title: service.title,
    description: service.description,
    image: service.image,
    path: `/services/${slug}`,
  });
}

/* =========================
   PAGE
========================= */
export default async function ServicePage({ params }) {
  const { slug } = await params;

  const data = await getService(slug, {
    next: { revalidate: 60 },
  });

  const service = data?.service;

  if (!service) return notFound();

  /* 🔥 SAFE DATA FIXES */
  const features = Array.isArray(service.features)
    ? service.features
    : [];

  const faq = Array.isArray(service.faq)
    ? service.faq
    : [];

  const seo = Array.isArray(service.seo)
    ? service.seo
    : service.seo
    ? [service.seo]
    : [];

  return (
    <main>

      {/* HERO */}
      <section className="bg-[#0B3C5D] text-white py-20">
        <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

          {/* LEFT */}
          <div>
            <h1 className="text-4xl md:text-5xl font-bold leading-tight">
              {service.title}
            </h1>

            <p className="mt-4 text-gray-300 text-lg">
              {service.description}
            </p>

            <a
              href={`tel:${service.phone || "+995599000000"}`}
              className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl hover:bg-[#00a892] transition"
            >
              {service.button_text || "📞 დაგვირეკე"}
            </a>
          </div>

          {/* RIGHT IMAGE */}
          <div className="relative w-full h-[300px] md:h-[400px] overflow-hidden rounded-2xl">
            <Image
              src={service.image || "/placeholder.jpg"}
              alt={service.title || "Service image"}
              fill
              sizes="(max-width: 768px) 100vw, 50vw"
              className="object-cover"
              priority
            />
          </div>

        </div>
      </section>

      {/* SHARE */}
      <Share data={data?.share ?? { title: "", buttons: [] }} />

      {/* FEATURES */}
      {features.length > 0 && (
        <FeaturesSection features={features} />
      )}

      {/* SEO */}
      {seo.length > 0 && (
        <SEOSection title={service.title} paragraphs={seo} />
      )}

      {/* FAQ */}
      {faq.length > 0 && (
        <FAQSection faq={faq} />
      )}

    </main>
  );
}