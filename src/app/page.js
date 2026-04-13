// import CTASection from "./components/home/CTASection";
// import FAQ from "./components/home/FAQ";
// import HeroSection from "./components/home/HeroSection";
// import HowItWorks from "./components/home/HowItWorks";
// import ServicesPreview from "./components/home/ServicesPreview";
// import WhyUs from "./components/home/WhyUs";
// import TrustSection from "./components/home/TrustSection";
// import StatsSection from "./components/home/StatsSection";
// import Testimonials from "./components/home/Testimonials";

// import { getHome, getSeoByKey, getEmpty } from "@/lib/datafetch";
// import { buildMetadata } from "@/lib/seo";
// import EmptyState from "../app/components/ui/EmptyState";
// import { injectInternalLinks } from "@/lib/internalLinks";
// import { getSeoLinks } from "@/lib/getSeoLinks";
// export const revalidate = 60;

// /* =========================
//    HELPER
// ========================= */
// const hasContent = (value) => {
//   if (!value) return false;

//   if (Array.isArray(value)) return value.length > 0;

//   if (typeof value === "object") {
//     return Object.values(value).some((v) => {
//       if (Array.isArray(v)) return v.length > 0;
//       if (typeof v === "object" && v !== null)
//         return Object.values(v).some(Boolean);
//       return Boolean(v);
//     });
//   }

//   return Boolean(value);
// };

// /* =========================
//    SEO
// ========================= */
// export async function generateMetadata() {
//   try {
//     const seo = await getSeoByKey("home");
//     const data = seo?.data;

//     return buildMetadata({
//       title: data?.title,
//       description: data?.description,
//       image: data?.og?.image,
//       keywords: data?.keywords,
//       canonical: data?.canonical,
//       noindex: data?.noindex,
//       og: data?.og,
//       path: data?.slug || "/",
//     });
//   } catch {
//     return buildMetadata({
//       title: "Home",
//       description: "Homepage",
//       path: "/",
//     });
//   }
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function Home() {
//   const keywordMap = await getSeoLinks();
//   let res = null;

//   try {
//     res = await getHome();
//   } catch {
//     res = null;
//   }

//   const data = res?.data || {};
//   const seo = res?.seo || {};

//   const isEmpty =
//     !hasContent(data?.homeHero) &&
//     !hasContent(data?.whyUs) &&
//     !hasContent(data?.services) &&
//     !hasContent(data?.howWork) &&
//     !hasContent(data?.trust) &&
//     !hasContent(data?.testimonials) &&
//     !hasContent(data?.stats) &&
//     !hasContent(data?.Cta) &&
//     !hasContent(data?.Faq);

//   /* ?? FIX  lazy getEmpty */
//   if (!res || res.error || isEmpty) {
//     const empty = await getEmpty().catch(() => null);
//     return <EmptyState empty={empty} />;
//   }

//   return (
//     <>
//       {/* ? SCHEMA */}
//       {seo?.schema &&
//         (Array.isArray(seo.schema) ? (
//           seo.schema.map((schema, i) => (
//             <script
//               key={i}
//               type="application/ld+json"
//               dangerouslySetInnerHTML={{
//                 __html: JSON.stringify(schema),
//               }}
//             />
//           ))
//         ) : (
//           <script
//             type="application/ld+json"
//             dangerouslySetInnerHTML={{
//               __html: JSON.stringify(seo.schema),
//             }}
//           />
//         ))}

//       <main>

//         {hasContent(data?.homeHero) && (
//           <HeroSection data={data.homeHero} />
//         )}

//         {hasContent(data?.whyUs) && (
//           <WhyUs data={data.whyUs} />
//         )}

//         {hasContent(data?.services) && (
//           <ServicesPreview data={data} />
//         )}

//         {hasContent(data?.howWork) && (
//           <HowItWorks data={data.howWork} />
//         )}

//         {hasContent(data?.trust) && (
//           <TrustSection data={data.trust} />
//         )}

//         {hasContent(data?.testimonials) && (
//           <Testimonials items={data.testimonials} />
//         )}

//         {hasContent(data?.stats) && (
//           <StatsSection data={data.stats} />
//         )}

//         {hasContent(data?.Cta) && (
//           <CTASection data={data.Cta} />
//         )}

//         {hasContent(data?.Faq) && (
//           <FAQ data={data.Faq} />
//         )}

//       </main>
//     </>
//   );
// }


import CTASection from "./components/home/CTASection";
import FAQ from "./components/home/FAQ";
import HeroSection from "./components/home/HeroSection";
import HowItWorks from "./components/home/HowItWorks";
import ServicesPreview from "./components/home/ServicesPreview";
import WhyUs from "./components/home/WhyUs";
import TrustSection from "./components/home/TrustSection";
import StatsSection from "./components/home/StatsSection";
import Testimonials from "./components/home/Testimonials";

import { getHome, getSeoByKey, getEmpty } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";
import EmptyState from "../app/components/ui/EmptyState";
import { injectInternalLinks } from "@/lib/internalLinks";
import { getSeoLinks } from "@/lib/getSeoLinks";
import Link from "next/link";
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
  try {
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
  } catch {
    return buildMetadata({
      title: "Home",
      description: "Homepage",
      path: "/",
    });
  }
}

/* =========================
   PAGE
========================= */
export default async function Home() {
  const keywordMap = await getSeoLinks();

  let res = null;

  try {
    res = await getHome();
  } catch {
    res = null;
  }

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

  return (
    <>
      {/* 🔥 SCHEMA */}
      {seo?.schema &&
        (Array.isArray(seo.schema) ? (
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
        ))}

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

        {/* =========================
           🔥 SEO + SILO SECTION
        ========================= */}
        {hasContent(data?.services) && (
          <section className="py-20 bg-white border-t">
            <div className="max-w-6xl mx-auto px-4">

              <h2 className="text-3xl font-bold text-[#0B3C5D] mb-8">
                IT სერვისები და ტექნიკური მხარდაჭერა თბილისში
              </h2>

              {/* 🔥 SEO TEXT */}
              <div
                className="text-gray-700 space-y-4 leading-relaxed"
                dangerouslySetInnerHTML={{
                  __html: injectInternalLinks(
                    `
ჩვენ გთავაზობთ სრულ IT სერვისებს თბილისში, რომელიც მოიცავს როგორც მცირე ოფისებს, ასევე დიდ ბიზნეს ინფრასტრუქტურას. 
ჩვენი გუნდი სპეციალიზირებულია ვიდეომეთვალყურეობის სისტემებში, კომპიუტერულ სერვისებში და ქსელების აწყობაში.

თუ გჭირდებათ IT მხარდაჭერა, POS სისტემების ინსტალაცია ან ქსელური ინფრასტრუქტურის გამართვა, ჩვენი კომპანია უზრუნველყოფს სწრაფ და საიმედო მომსახურებას.

ჩვენი მიზანია უზრუნველვყოთ თქვენი ბიზნესის უწყვეტი მუშაობა თანამედროვე ტექნოლოგიების გამოყენებით.
                    `,
                    keywordMap
                  ),
                }}
              />

              {/* 🔥 CATEGORY LINKS (SILO) */}
              <div className="mt-10 grid md:grid-cols-3 gap-6">

                <Link
                  href="/services/category/kompiuteruli-servisebi"
                  className="p-6 border rounded-xl hover:shadow-lg transition"
                >
                  <h3 className="font-bold text-[#0B3C5D]">
                    კომპიუტერული სერვისები
                  </h3>
                  <p className="text-sm text-gray-600 mt-2">
                    Windows ინსტალაცია და ტექნიკური მხარდაჭერა
                  </p>
                </Link>

                <Link
                  href="/services/category/usafrtkhoebis-sistemebi"
                  className="p-6 border rounded-xl hover:shadow-lg transition"
                >
                  <h3 className="font-bold text-[#0B3C5D]">
                    უსაფრთხოების სისტემები
                  </h3>
                  <p className="text-sm text-gray-600 mt-2">
                    ვიდეომეთვალყურეობა და კამერების მონტაჟი
                  </p>
                </Link>

                <Link
                  href="/services/category/qselebi-da-infrastruqtura"
                  className="p-6 border rounded-xl hover:shadow-lg transition"
                >
                  <h3 className="font-bold text-[#0B3C5D]">
                    ქსელები და ინფრასტრუქტურა
                  </h3>
                  <p className="text-sm text-gray-600 mt-2">
                    LAN ქსელები და IT ინფრასტრუქტურა
                  </p>
                </Link>

              </div>

            </div>
          </section>
        )}

      </main>
    </>
  );
}