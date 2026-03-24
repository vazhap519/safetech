// import CTASection from "./components/home/CTASection";
// import FAQ from "./components/home/FAQ";
// import HeroSection from "./components/home/HeroSection";
// import HowItWorks from "./components/home/HowItWorks";
// import ServicesPreview from "./components/home/ServicesPreview";
// import WhyUs from "./components/home/WhyUs";
// import { getHome } from "../lib/datafetch";
// import { buildMetadata } from "@/lib/seo";
// import { getService } from "@/lib/datafetch";
// /* =========================
//    META SEO (HOME)
// ========================= */
// export const metadata = buildMetadata({
//   title: "IT სერვისები და კამერების მონტაჟი თბილისში",
//   description:
//     "Safetech გთავაზობთ კამერების მონტაჟს, POS სისტემებს, ინტერნეტის გაყვანას და სრულ IT სერვისებს საქართველოში.",
//   path: "",
// });

// /* =========================
//    PAGE
// ========================= */
// export default async function Home() {
//   const data = await getHome();
// const dataService = await getService(slug);
// const service = dataService?.service;
//   return (
//     <main>

//       {/* JSON-LD (Organization) */}
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

//       {/* JSON-LD (LocalBusiness) */}
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

//       <HeroSection data={data?.homeHero} />
//       {/* <ServicesPreview  data={data?.services}/> */}
//       <ServicesPreview dataService={dataService} />
//       <WhyUs data={data?.HomeWhyUs}/>
//       <HowItWorks data={data?.howWork} />
//       <CTASection data={data?.Cta} />
//       <FAQ  data={data?.Faq}/>

//     </main>
//   );
// }

import CTASection from "./components/home/CTASection";
import FAQ from "./components/home/FAQ";
import HeroSection from "./components/home/HeroSection";
import HowItWorks from "./components/home/HowItWorks";
import ServicesPreview from "./components/home/ServicesPreview";
import WhyUs from "./components/home/WhyUs";

import { getHome } from "../lib/datafetch";
import { buildMetadata } from "@/lib/seo";

/* =========================
   META SEO (HOME)
========================= */
export const metadata = buildMetadata({
  title: "IT სერვისები და კამერების მონტაჟი თბილისში",
  description:
    "Safetech გთავაზობთ კამერების მონტაჟს, POS სისტემებს, ინტერნეტის გაყვანას და სრულ IT სერვისებს საქართველოში.",
  path: "",
});

/* =========================
   PAGE
========================= */
export default async function Home() {
 const data = await getHome({
  next: { revalidate: 60 },
});
console.log('home',data);
  return (
    <main>

      {/* JSON-LD (Organization) */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Organization",
            name: "Safetech",
            url: "https://safetech.ge",
            logo: "https://safetech.ge/logo.png",
            contactPoint: {
              "@type": "ContactPoint",
              telephone: "+995599000000",
              contactType: "customer service",
            },
          }),
        }}
      />

      {/* JSON-LD (LocalBusiness) */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            name: "Safetech",
            areaServed: "Georgia",
            telephone: "+995599000000",
          }),
        }}
      />

      {/* HERO */}
      <HeroSection data={data?.homeHero} />

      {/* SERVICES */}
      <ServicesPreview data={data} />

      {/* WHY US */}
      <WhyUs data={data?.whyUs} />

      {/* HOW IT WORKS */}
      <HowItWorks data={data?.howWork} />

      {/* CTA */}
      <CTASection data={data?.Cta} />

      {/* FAQ */}
      <FAQ data={data?.Faq} />

    </main>
  );
}