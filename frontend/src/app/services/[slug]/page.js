import { notFound } from "next/navigation";
import Image from "next/image";

import { servicesData } from "@/data/services";
import { buildMetadata } from "@/lib/seo";

import FeaturesSection from "@/app/components/services/FeaturesSection";
import FAQSection from "@/app/components/services/FAQSection";
import SEOSection from "@/app/components/services/SEOSection";
import CopyButton from "@/app/components/services/CopyButton";

/* =========================
   META SEO
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;
  const service = servicesData[slug];

  return buildMetadata({
    title: service?.title,
    description: service?.desc,
    image: service?.image,
    path: `/services/${slug}`,
  });
}

/* =========================
   PAGE
========================= */
export default async function ServicePage({ params }) {
  const { slug } = await params;
  const service = servicesData[slug];

  if (!service) return notFound();

  return (
    <main>

      {/* JSON-LD (Service) */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Service",
            name: service.title,
            description: service.desc,
            areaServed: "Georgia",
            provider: {
              "@type": "Organization",
              name: "Safetech",
            },
          }),
        }}
      />

      {/* JSON-LD (FAQ) */}
      {service.faq?.length > 0 && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "FAQPage",
              mainEntity: service.faq.map((item) => ({
                "@type": "Question",
                name: item.q,
                acceptedAnswer: {
                  "@type": "Answer",
                  text: item.a,
                },
              })),
            }),
          }}
        />
      )}

      {/* HERO */}
      <section className="bg-[#0B3C5D] text-white py-20">
        <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

          <div>
            <h1 className="text-4xl md:text-5xl font-bold leading-tight">
              {service.title}
            </h1>

            <p className="mt-4 text-gray-300 text-lg">
              {service.desc}
            </p>

            <a
              href="tel:+995599000000"
              className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl hover:bg-[#00a892] transition"
            >
              📞 დაგვირეკე
            </a>
          </div>

          {/* 🔥 FIX: Next Image instead of img */}
          <div className="relative w-full h-[300px]">
            <Image
              src={service.image}
              alt={service.title}
              fill
              className="rounded-2xl shadow-lg object-cover"
              priority
            />
          </div>

        </div>
      </section>
{/* 🔥 SHARE */}
<div className="bg-white py-6 border-b">
  <div className="max-w-7xl mx-auto px-4 flex flex-col items-center gap-4 text-center">

    <p className="text-sm text-gray-600">
      გააზიარე ეს სერვისი:
    </p>

    <div className="flex flex-wrap justify-center gap-3">

      <a
        href={`https://www.facebook.com/sharer/sharer.php?u=https://safetech.ge/services/${slug}`}
        target="_blank"
        className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:scale-105 transition"
      >
        Facebook
      </a>

      <a
        href={`https://api.whatsapp.com/send?text=https://safetech.ge/services/${slug}`}
        target="_blank"
        className="px-4 py-2 bg-green-500 text-white rounded-lg text-sm hover:scale-105 transition"
      >
        WhatsApp
      </a>

      <a
        href={`https://t.me/share/url?url=https://safetech.ge/services/${slug}`}
        target="_blank"
        className="px-4 py-2 bg-sky-500 text-white rounded-lg text-sm hover:scale-105 transition"
      >
        Telegram
      </a>

<CopyButton url={`https://safetech.ge/services/${slug}`} />
    </div>

  </div>
</div>
      {/* FEATURES */}
      {service.features?.length > 0 && (
        <FeaturesSection features={service.features} />
      )}

      {/* SEO TEXT */}
      {service.seo?.length > 0 && (
        <SEOSection title={service.title} paragraphs={service.seo} />
      )}

      {/* FAQ */}
      {service.faq?.length > 0 && (
        <FAQSection faq={service.faq} />
      )}

      {/* CTA */}
      <section className="py-20 bg-[#0B3C5D] text-white text-center">
        <h2 className="text-3xl font-bold">
          დაგვიკავშირდი დღესვე
        </h2>

        <p className="mt-4 text-gray-300">
          მიიღე პროფესიონალური IT მომსახურება სწრაფად და ხარისხიანად
        </p>

        <a
          href="tel:+995599000000"
          className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl hover:bg-[#00a892] transition"
        >
          📞 დაგვირეკე
        </a>
      </section>

    </main>
  );
}