import { buildMetadata } from "@/lib/seo";
import Script from "next/script";

import AboutCTA from "../components/about/AboutCTA";
import AboutHero from "../components/about/AboutHero";
import AboutStory from "../components/about/AboutStory";
import AboutWhyUs from "../components/about/AboutWhyUs";
import { getAbout } from "@/lib/datafetch";
/* =========================
   META SEO
========================= */
export const metadata = buildMetadata({
  title: "ჩვენს შესახებ",
  description:
    "Safetech არის IT სერვისების კომპანია საქართველოში, რომელიც გთავაზობთ კამერების მონტაჟს და ტექნიკურ მხარდაჭერას.",
  path: "/about",
});

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