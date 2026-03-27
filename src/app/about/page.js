
import Script from "next/script";

import AboutCTA from "../components/about/AboutCTA";
import AboutHero from "../components/about/AboutHero";
import AboutStory from "../components/about/AboutStory";
import AboutWhyUs from "../components/about/AboutWhyUs";
import { getAbout ,getSeoByKey} from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";
/* =========================
   META SEO
========================= */
/* =========================
   SEO (SERVICES 🔥)
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
export default async function AboutPage() {
  const About=await getAbout();
  console.log('AboutUs',About)
const Hero=About?.Hero;
const Story=About?.Story;
const Why=About?.Why;
const Cta = About?.Cta;
  return (
    <main>

      {/* JSON-LD */}
      <Script
        id="about-schema"
        type="application/ld+json"
        strategy="afterInteractive"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Organization",
            name: "Safetech",
            url: "https://safetech.ge",
            telephone: "+995599000000",
            areaServed: "Georgia",
          }),
        }}
      />

      <AboutHero Hero={Hero}/>
      <AboutStory Story={Story}/>
      <AboutWhyUs Why={Why} />
      <AboutCTA Cta={Cta}/>

    </main>
  );
}