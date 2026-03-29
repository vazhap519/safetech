
// import Script from "next/script";

// import AboutCTA from "../components/about/AboutCTA";
// import AboutHero from "../components/about/AboutHero";
// import AboutStory from "../components/about/AboutStory";
// import AboutWhyUs from "../components/about/AboutWhyUs";
// import { getAbout ,getSeoByKey} from "@/lib/datafetch";
// import { buildMetadata } from "@/lib/seo";
// /* =========================
//    META SEO
// ========================= */
// /* =========================
//    SEO (BLOG 🔥)
// ========================= */
// export async function generateMetadata() {
//   const seo = await getSeoByKey("about");
//   const data = seo?.data;

//   return buildMetadata({
//     title: data?.title,
//     description: data?.description,
//     image: data?.og?.image,
//     keywords: data?.keywords,
//     canonical: data?.canonical,
//     noindex: data?.noindex,
//     og: data?.og,
//     path: data?.slug || "/about",
//   });
// }
// export default async function AboutPage() {
//   const About=await getAbout();
//   console.log('AboutUs',About)
// const Hero=About?.Hero;
// const Story=About?.Story;
// const Why=About?.Why;
// const Cta = About?.Cta;
//   return (
//     <main>

//       {/* JSON-LD */}
//       <Script
//         id="about-schema"
//         type="application/ld+json"
//         strategy="afterInteractive"
//         dangerouslySetInnerHTML={{
//           __html: JSON.stringify({
//             "@context": "https://schema.org",
//             "@type": "Organization",
//             name: "Safetech",
//             url: "https://safetech.ge",
//             telephone: "+995599000000",
//             areaServed: "Georgia",
//           }),
//         }}
//       />

//       <AboutHero Hero={Hero}/>
//       <AboutStory Story={Story}/>
//       <AboutWhyUs Why={Why} />
//       <AboutCTA Cta={Cta}/>

//     </main>
//   );
// }

import AboutCTA from "../components/about/AboutCTA";
import AboutHero from "../components/about/AboutHero";
import AboutStory from "../components/about/AboutStory";
import AboutWhyUs from "../components/about/AboutWhyUs";

import { getAbout, getSeoByKey } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";

/* =========================
   SEO
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("about");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/about",
  });
}

/* =========================
   PAGE
========================= */
export default async function AboutPage() {

  // ✅ backend unified response (როგორც services/blog)
  const res = await getAbout();

  if (!res || res.error) {
    return (
      <div className="text-center py-20 text-red-500">
        გვერდი ვერ ჩაიტვირთა 😔
      </div>
    );
  }

  const data = res.data;
  const seo = res.seo;

  const Hero = data?.Hero;
  const Story = data?.Story;
  const Why = data?.Why;
  const Cta = data?.Cta;

  return (
    <>
      {/* 🔥 DYNAMIC SCHEMA */}
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
        <AboutHero Hero={Hero} />
        <AboutStory Story={Story} />
        <AboutWhyUs Why={Why} />
        <AboutCTA Cta={Cta} />
      </main>
    </>
  );
}