import CTASection from "./components/home/CTASection";
import FAQ from "./components/home/FAQ";
import HeroSection from "./components/home/HeroSection";
import HowItWorks from "./components/home/HowItWorks";
import ServicesPreview from "./components/home/ServicesPreview";
import WhyUs from "./components/home/WhyUs";
import TrustSection from "./components/home/TrustSection";
import StatsSection from "./components/home/StatsSection";
import Testimonials from "./components/home/Testimonials";
import EmptyState from "../app/components/ui/EmptyState";
import Link from "next/link";

import { getHome, getSeoByKey, getEmpty } from "@/lib/datafetch";
import { getBaseUrl } from "@/lib/config";
import { getSeoLinks } from "@/lib/getSeoLinks";
import { injectInternalLinks } from "@/lib/internalLinks";
import { buildMetadata } from "@/lib/seo";

export const revalidate = 60;

const HOME_SEO_TEXT = `
<p>Safetech ეხმარება კომპანიებს თბილისში და მთელ საქართველოში, რომ IT ინფრასტრუქტურა, უსაფრთხოების სისტემები და საოფისე ტექნიკა იმუშაოს სტაბილურად, სწრაფად და უსაფრთხოდ.</p>
<p>ჩვენი მომსახურება მოიცავს კომპიუტერულ სერვისებს, ქსელების გამართვას, ვიდეომეთვალყურეობის სისტემებს, POS სისტემებს, სერვერების მხარდაჭერას და ბიზნესისთვის საჭირო ტექნიკური პროცესების მართვას.</p>
<p>თუ გჭირდებათ IT მხარდაჭერა, კამერების მონტაჟი, ქსელური ინფრასტრუქტურის გამართვა ან კომპიუტერული ტექნიკის მომსახურება, Safetech გაძლევთ ერთიან გუნდს, რომელიც პრობლემას აანალიზებს, გეგმავს და ბოლომდე აგვარებს.</p>
`;

const SILO_LINKS = [
  {
    href: "/services/category/kompiuteruli-servisebi",
    title: "კომპიუტერული სერვისები",
    description: "Windows-ის ინსტალაცია, დიაგნოსტიკა, ტექნიკური მხარდაჭერა და საოფისე კომპიუტერების გამართვა.",
  },
  {
    href: "/services/category/usafrtkhoebis-sistemebi",
    title: "უსაფრთხოების სისტემები",
    description: "ვიდეომეთვალყურეობის სისტემები, კამერების მონტაჟი და ობიექტის უსაფრთხოების ტექნიკური გადაწყვეტები.",
  },
  {
    href: "/services/category/qselebi-da-infrastruqtura",
    title: "ქსელები და ინფრასტრუქტურა",
    description: "LAN ქსელები, Wi-Fi, სერვერული კარადები და ბიზნესისთვის სტაბილური IT ინფრასტრუქტურა.",
  },
];

const hasContent = (value) => {
  if (!value) return false;
  if (Array.isArray(value)) return value.length > 0;

  if (typeof value === "object") {
    return Object.values(value).some((v) => {
      if (Array.isArray(v)) return v.length > 0;
      if (typeof v === "object" && v !== null) return Object.values(v).some(Boolean);
      return Boolean(v);
    });
  }

  return Boolean(value);
};

const normalizeSchema = (schema) => {
  if (!schema) return [];
  return Array.isArray(schema) ? schema.filter(Boolean) : [schema];
};

const buildHomeSchemas = ({ apiSchema, faq }) => {
  const baseUrl = getBaseUrl();
  const schemas = [
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "@id": `${baseUrl}/#organization`,
      name: "Safetech",
      url: baseUrl,
      areaServed: "Georgia",
    },
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "@id": `${baseUrl}/#website`,
      name: "Safetech",
      url: baseUrl,
      publisher: { "@id": `${baseUrl}/#organization` },
      inLanguage: "ka-GE",
    },
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "@id": `${baseUrl}/#webpage`,
      url: baseUrl,
      name: "Safetech - IT სერვისები და უსაფრთხოების სისტემები",
      isPartOf: { "@id": `${baseUrl}/#website` },
      about: { "@id": `${baseUrl}/#organization` },
      inLanguage: "ka-GE",
    },
    ...normalizeSchema(apiSchema),
  ];

  if (Array.isArray(faq?.faq) && faq.faq.length > 0) {
    schemas.push({
      "@context": "https://schema.org",
      "@type": "FAQPage",
      mainEntity: faq.faq
        .filter((item) => item?.question && item?.answer)
        .map((item) => ({
          "@type": "Question",
          name: item.question,
          acceptedAnswer: {
            "@type": "Answer",
            text: item.answer,
          },
        })),
    });
  }

  return schemas;
};

export async function generateMetadata() {
  try {
    const seo = await getSeoByKey("home");
    const data = seo?.data;

    return buildMetadata({
      title: data?.title || "IT სერვისები და უსაფრთხოების სისტემები",
      description:
        data?.description ||
        "Safetech გთავაზობთ IT მხარდაჭერას, კომპიუტერულ სერვისებს, ქსელების გამართვას და უსაფრთხოების სისტემების მონტაჟს საქართველოში.",
      image: data?.og?.image,
      keywords: data?.keywords,
      canonical: data?.canonical,
      noindex: data?.noindex,
      og: data?.og,
      path: "/",
    });
  } catch {
    return buildMetadata({
      title: "IT სერვისები და უსაფრთხოების სისტემები",
      description:
        "Safetech გთავაზობთ IT მხარდაჭერას, კომპიუტერულ სერვისებს, ქსელების გამართვას და უსაფრთხოების სისტემების მონტაჟს საქართველოში.",
      path: "/",
    });
  }
}

export default async function Home() {
  const [keywordMap, res] = await Promise.all([
    getSeoLinks(),
    getHome().catch(() => null),
  ]);

  const data = res?.data || {};
  const seo = res?.seo || {};

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

  if (!res || res.error || isEmpty) {
    const empty = await getEmpty().catch(() => null);
    return <EmptyState empty={empty} />;
  }

  const schemas = buildHomeSchemas({ apiSchema: seo?.schema, faq: data?.Faq });
  const linkedSeoText = injectInternalLinks(HOME_SEO_TEXT, Array.isArray(keywordMap) ? keywordMap : []);

  return (
    <>
      {schemas.map((schema, i) => (
        <script
          key={`home-schema-${i}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
        />
      ))}

      <div>
        {hasContent(data?.homeHero) && <HeroSection data={data.homeHero} />}
        {hasContent(data?.whyUs) && <WhyUs data={data.whyUs} />}
        {hasContent(data?.services) && <ServicesPreview data={data} />}
        {hasContent(data?.howWork) && <HowItWorks data={data.howWork} />}
        {hasContent(data?.trust) && <TrustSection data={data.trust} />}
        {hasContent(data?.testimonials) && <Testimonials items={data.testimonials} />}
        {hasContent(data?.stats) && <StatsSection data={data.stats} />}
        {hasContent(data?.Cta) && <CTASection data={data.Cta} />}
        {hasContent(data?.Faq) && <FAQ data={data.Faq} />}

        <section className="py-20 bg-white border-t border-gray-100">
          <div className="max-w-6xl mx-auto px-4">
            <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-2">
              IT მომსახურება საქართველოში
            </p>
            <h2 className="text-3xl font-bold text-[#0B3C5D] mb-6">
              IT სერვისები, უსაფრთხოების სისტემები და ტექნიკური მხარდაჭერა ბიზნესისთვის
            </h2>

            <div
              className="text-gray-700 space-y-4 leading-relaxed"
              dangerouslySetInnerHTML={{ __html: linkedSeoText }}
            />

            <div className="mt-10 grid md:grid-cols-3 gap-6">
              {SILO_LINKS.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className="p-6 border border-gray-100 rounded-lg hover:shadow-lg transition bg-white"
                >
                  <h3 className="font-bold text-[#0B3C5D]">{item.title}</h3>
                  <p className="text-sm text-gray-600 mt-2 leading-relaxed">
                    {item.description}
                  </p>
                </Link>
              ))}
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
